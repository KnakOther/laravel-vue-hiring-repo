<?php

namespace App\Services;

use App\Models\Recipient;
use Illuminate\Support\Facades\Log;

class EmailRenderService
{
    public function __invoke(string $recipient_id, string $email_raw_html) {
        $recipient = Recipient::findOrFail($recipient_id);
        $email_raw_html = preg_replace("/\s+|\n+|\r/", ' ', $email_raw_html);
        file_put_contents(storage_path('logs/email_render_service_before.log'), $email_raw_html);
        $email_raw_html = $this->replaceHtmlWithMergeTags($email_raw_html, $recipient);
        $email_raw_html = $this->replaceFreeMarkerTags($email_raw_html, $recipient);
        //remove track ${track('https://giphy.com/gifs/theoffice-6v2UJRyFAsTXgvJrin?3rH1par9ka', 'image_663154dcd3208')} but keep the url
        $email_raw_html = preg_replace('/\${track\(\'(.*?)\', \'(.*?)\'\)}/', '$1', $email_raw_html);

        file_put_contents(storage_path('logs/email_render_service_after.log'), $email_raw_html);
        return $email_raw_html;
    }

    private function replaceHtmlWithMergeTags(string $html, Recipient $recipient): string
    {
        $replaceable_keys = [
            'first_name',
            'last_name',
            'email',
            'department',
            'position',
            'location',
        ];
        $recipient_keys = $recipient->toArray();
        foreach ($replaceable_keys as $key) {
            $key_to_replace = $this->transformKey($key);
            $html = str_replace('${' . $key_to_replace . '}', $recipient_keys[$key], $html);
        }
        return $html;
    }

    public function replaceFreeMarkerTags($html, $recipient) {
        // if equal
        // find if statements and content <#if $key?? && $key?string?lower_case == "$value">$content</#if>
        preg_match_all("<#if\s\((\w+)\?\?\s\&\&\s(\w+)\?\w+\?\w+\s\=\=\s\"(.*?)\"\)\>(.*?)<#else>(.*?)\<\/#if>", $html, $output_array);

        foreach ($output_array[0] as $key => $value) {
            $recKey = strtolower($output_array[1][$key]);
            $value = strtolower($output_array[3][$key]);

            if ( strtolower($recipient->$recKey) == $value) {
                $html = str_replace($output_array[0][$key], $output_array[4][$key], $html);
            } else {
                $html = str_replace($output_array[0][$key], $output_array[5][$key], $html);
            }
        }

        // if not equal
        // find if statements and content <#if $key?? && $key?string?lower_case == "$value">$content</#if>
        preg_match_all("<#if\s\((\w+)\?\?\s\&\&\s(\w+)\?\w+\?\w+\s\!\=\s\"(.*?)\"\)\>(.*?)<#else>(.*?)\<\/#if>", $html, $output_array);

        foreach ($output_array[0] as $key => $value) {
            $recKey = strtolower($output_array[1][$key]);
            $value = strtolower($output_array[3][$key]);

            if ( strtolower($recipient->$recKey) == $value) {
                $html = str_replace($output_array[0][$key], $output_array[5][$key], $html);
            } else {
                $html = str_replace($output_array[0][$key], $output_array[4][$key], $html);
            }
        }
        return $html;
    }

    private function transformKey($key)
    {
        return implode('_', array_map('ucfirst', explode('_', $key)));
    }
}
