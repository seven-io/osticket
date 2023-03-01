<?php
require_once INCLUDE_DIR . 'class.forms.php';

class SevenPluginConfig extends PluginConfig {
    public const KEY_API_KEY = 'apiKey';

    function translate(): array {
        if (!method_exists('Plugin', 'translate')) {  // Provide translation compatibility osTicket < v1.9.4
            return [
                function ($x) {
                    return $x;
                },
                function ($x, $y, $n) {
                    return $n != 1 ? $y : $x;
                },
            ];
        }

        return Plugin::translate('seven');
    }

    public function getApiKey(): ?string {
        return $this->get(self::KEY_API_KEY);
    }

    function getOptions(): array {
        [$__] = self::translate();

        return [
            'credentials' => new SectionBreakField([
                'label' => $__('seven Credentials'),
            ]),
            self::KEY_API_KEY => new PasswordField([
                'configuration' => [
                    'length' => 90,
                    'size' => 90,
                ],
                'hint' => $__('Can be created in your seven dashboard.'),
                'label' => $__('API Key'),
            ]),
        ];
    }

    function pre_save(&$config, &$errors): bool {
        global $msg;

        [$__] = self::translate();
        $apiKey = &$config[self::KEY_API_KEY];

        $ch = curl_init('https://gateway.seven.io/api/balance');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'X-Api-Key: ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);

        $invalidKeyErrorMessage = $__('The API key seems to be invalid.');
        try {
            $json = json_decode($json, null, 512, JSON_THROW_ON_ERROR);

            if (is_int($json)) {
                $apiKey = '';
                $errors['err'] = $invalidKeyErrorMessage;

                return false;
            }
        } catch (JsonException) {
            $errors['err'] = $invalidKeyErrorMessage;
        }

        if (!$errors) $msg = $__('Configuration updated successfully');

        return true;
    }
}
