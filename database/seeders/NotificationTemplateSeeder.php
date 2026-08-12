<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (NotificationTemplate::catalog() as $key => [$audience, $label, $desc, $subject, $body, $wa]) {
            NotificationTemplate::firstOrCreate(
                ['event_key' => $key],
                [
                    'audience'               => $audience,
                    'label'                  => $label,
                    'description'            => $desc,
                    'email_enabled'          => true,
                    'email_subject'          => $subject,
                    'email_body'             => $body,
                    'whatsapp_enabled'       => false,
                    'whatsapp_template_name' => $wa,
                    'whatsapp_body_params'   => $this->extractPlaceholders($body),
                    'inapp_enabled'          => true,
                ]
            );
        }
    }

    protected function extractPlaceholders(string $body): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $body, $m);
        $unique = array_values(array_unique($m[1] ?? []));
        // Common WA templates expect max ~4 body params — keep the most useful.
        return array_map(fn ($k) => '{{'.$k.'}}', array_slice($unique, 0, 8));
    }
}
