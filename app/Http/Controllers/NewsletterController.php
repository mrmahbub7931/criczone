<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    /** POST /api/newsletter/subscribe — public */
    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:100',
        ]);

        $existing = NewsletterSubscriber::where('email', $data['email'])->first();

        if ($existing) {
            if ($existing->status === 'subscribed') {
                return response()->json(['message' => 'You are already subscribed!'], 200);
            }
            $existing->update(['status' => 'subscribed', 'confirmed_at' => now()]);
            return response()->json(['message' => 'Welcome back! You have been re-subscribed.']);
        }

        $subscriber = NewsletterSubscriber::create([
            'email'        => $data['email'],
            'name'         => $data['name'] ?? null,
            'status'       => 'subscribed',
            'confirmed_at' => now(),
            'ip'           => $request->ip(),
        ]);

        // Send welcome email if SMTP configured
        $this->sendWelcomeEmail($subscriber);

        return response()->json(['message' => 'Successfully subscribed! Welcome to CricZone.'], 201);
    }

    /** GET /api/newsletter/unsubscribe/{token} — public */
    public function unsubscribe(string $token): JsonResponse
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->first();
        if (! $subscriber) {
            return response()->json(['message' => 'Invalid unsubscribe link.'], 404);
        }
        $subscriber->update(['status' => 'unsubscribed']);
        return response()->json(['message' => 'You have been unsubscribed successfully.']);
    }

    /** GET /api/admin/newsletter/subscribers — admin */
    public function index(Request $request): JsonResponse
    {
        $subscribers = NewsletterSubscriber::query()
            ->when($request->search, fn ($q, $s) => $q->where('email', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json($subscribers);
    }

    /** DELETE /api/admin/newsletter/subscribers/{subscriber} — admin */
    public function destroy(NewsletterSubscriber $subscriber): JsonResponse
    {
        $subscriber->delete();
        return response()->json(null, 204);
    }

    /** POST /api/admin/newsletter/send — admin: send newsletter to all subscribers */
    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $smtp = Setting::where('group', 'smtp')->get()->pluck('value', 'key');

        if (empty($smtp['smtp_host']) || empty($smtp['smtp_username'])) {
            return response()->json(['message' => 'SMTP is not configured. Please set up SMTP in Settings → Email & SMTP.'], 422);
        }

        $this->configureSmtp($smtp);

        $prefix      = Setting::where('key', 'newsletter_subject_prefix')->value('value') ?? '';
        $subject     = trim("{$prefix} {$data['subject']}");
        $fromEmail   = $smtp['smtp_from_email'];
        $fromName    = $smtp['smtp_from_name'] ?? 'CricZone';
        $subscribers = NewsletterSubscriber::where('status', 'subscribed')->get();
        $sent        = 0;
        $failed      = 0;

        foreach ($subscribers as $sub) {
            try {
                $unsubUrl = url("/newsletter/unsubscribe/{$sub->token}");
                $html     = nl2br(e($data['body'])) . "<br><br><small><a href='{$unsubUrl}'>Unsubscribe</a></small>";
                Mail::send([], [], function ($msg) use ($sub, $subject, $fromEmail, $fromName, $html) {
                    $msg->to($sub->email, $sub->name ?? '')
                        ->from($fromEmail, $fromName)
                        ->subject($subject)
                        ->html($html);
                });
                $sent++;
            } catch (\Throwable) {
                $failed++;
            }
        }

        return response()->json([
            'message' => "Newsletter sent to {$sent} subscribers." . ($failed ? " {$failed} failed." : ''),
            'sent'    => $sent,
            'failed'  => $failed,
        ]);
    }

    // ── SMTP test ────────────────────────────────────────────────────────────

    /** POST /api/settings/test-smtp — admin */
    public function testSmtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'test_email'      => 'required|email',
            'smtp_host'       => 'required|string',
            'smtp_port'       => 'required|numeric',
            'smtp_username'   => 'required|string',
            'smtp_password'   => 'required|string',
            'smtp_encryption' => 'nullable|in:tls,ssl,',
            'smtp_from_email' => 'required|email',
            'smtp_from_name'  => 'nullable|string',
        ]);

        $this->configureSmtp($data);

        try {
            Mail::send([], [], function ($msg) use ($data) {
                $msg->to($data['test_email'])
                    ->from($data['smtp_from_email'], $data['smtp_from_name'] ?? 'CricZone')
                    ->subject('CricZone SMTP Test')
                    ->html('<p>Your SMTP configuration is working correctly! ✅</p><p>Sent from <strong>CricZone</strong> settings.</p>');
            });
            return response()->json(['message' => 'Test email sent successfully! Check your inbox.']);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'SMTP test failed: ' . $e->getMessage()], 422);
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function configureSmtp(mixed $smtp): void
    {
        $encryption = $smtp['smtp_encryption'] ?? 'tls';

        config([
            'mail.default'                       => 'smtp',
            'mail.mailers.smtp.host'             => $smtp['smtp_host']     ?? '',
            'mail.mailers.smtp.port'             => (int) ($smtp['smtp_port'] ?? 587),
            'mail.mailers.smtp.username'         => $smtp['smtp_username'] ?? '',
            'mail.mailers.smtp.password'         => $smtp['smtp_password'] ?? '',
            'mail.mailers.smtp.encryption'       => $encryption ?: null,
            'mail.from.address'                  => $smtp['smtp_from_email'] ?? '',
            'mail.from.name'                     => $smtp['smtp_from_name'] ?? 'CricZone',
        ]);
    }

    private function sendWelcomeEmail(NewsletterSubscriber $subscriber): void
    {
        try {
            $smtp = Setting::where('group', 'smtp')->get()->pluck('value', 'key');
            if (empty($smtp['smtp_host']) || empty($smtp['smtp_username'])) return;

            $this->configureSmtp($smtp);

            $subject     = Setting::where('key', 'newsletter_welcome_subject')->value('value') ?? 'Welcome to CricZone!';
            $body        = Setting::where('key', 'newsletter_welcome_body')->value('value')    ?? 'Thank you for subscribing!';
            $fromEmail   = $smtp['smtp_from_email'];
            $fromName    = $smtp['smtp_from_name'] ?? 'CricZone';
            $unsubUrl    = url("/newsletter/unsubscribe/{$subscriber->token}");
            $html        = nl2br(e($body)) . "<br><br><small><a href='{$unsubUrl}'>Unsubscribe</a></small>";

            Mail::send([], [], function ($msg) use ($subscriber, $subject, $fromEmail, $fromName, $html) {
                $msg->to($subscriber->email, $subscriber->name ?? '')
                    ->from($fromEmail, $fromName)
                    ->subject($subject)
                    ->html($html);
            });
        } catch (\Throwable) {
            // Silent — welcome email failure must not block the subscription
        }
    }
}
