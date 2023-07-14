<?php
require_once INCLUDE_DIR . 'class.forms.php';

class SevenPluginConfig extends PluginConfig {
    public const KEY_API_KEY = 'apiKey';
    public const KEY_ON_NEW_TICKET = 'onNewTicket';
    public const KEY_EVENT_MAIL_GROUP_ID = 'eventTemplateId';

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

    public function getEventMailGroupId(): ?int {
        return $this->get(self::KEY_EVENT_MAIL_GROUP_ID);
    }

    public function getOnNewTicket(): bool {
        return (bool)$this->get(self::KEY_ON_NEW_TICKET);
    }

    function getOptions(): array {
        [$__] = self::translate();
        $cfg = new OsticketConfig;
        $primaryLanguage = $cfg->getPrimaryLanguage();
        $sql=sprintf('SELECT tpl.*,count(dept.tpl_id) as depts '.
            'FROM '.EMAIL_TEMPLATE_GRP_TABLE.' tpl '.
            'LEFT JOIN '.DEPT_TABLE.' dept USING(tpl_id) '.
            'WHERE isactive=1 AND lang=\'%s\' GROUP BY tpl.tpl_id', $primaryLanguage);
        $res=db_query($sql);
        $tplChoices = [];

        if($res) {
            while ($row = db_fetch_array($res)) {
                $tplChoices[$row['tpl_id']] = $row['name'];
            }
        }

        return [
            'credentials' => new SectionBreakField([
                'label' => $__('seven Credentials'),
            ]),
            self::KEY_API_KEY => new PasswordField([
                'configuration' => [
                    'autofocus' => !$this->getApiKey(),
                    'length' => 90,
                    'size' => 90,
                ],
                'hint' => $__('Can be created in your seven dashboard.'),
                'label' => $__('API Key'),
                'required' => !$this->getApiKey(),
                'validator' => 'noop',
            ]),
            'events' => new SectionBreakField([
                'label' => $__('Events'),
            ]),
            self::KEY_EVENT_MAIL_GROUP_ID => new ChoiceField([
                'choices' => $tplChoices,
                'hint' => $__('Mail template used for event-based SMS dispatch.'),
                'label' => $__('Select Mail Template'),
            ]),
            self::KEY_ON_NEW_TICKET => new BooleanField([
                'hint' => $__('Sends an SMS to the associated agent after a new ticket got submitted.'),
                'label' => $__('Send SMS on new Ticket?'),
            ]),
        ];
    }

    public function request(string $endpoint, array $body, string $apiKey = null): mixed {
        $apiKey = $apiKey ?? $this->getApiKey();
        $headers = [
            'Accept: application/json',
            'Content-type: application/json',
            'X-Api-Key: ' . $apiKey,
        ];
        $ch = curl_init('https://gateway.seven.io/api/' . $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }
        $json = curl_exec($ch);
        curl_close($ch);

        try {
            $json = json_decode($json, null, 512, JSON_THROW_ON_ERROR);

            if (is_int($json)) return null;
        } catch (JsonException) {
            return null;
        }

        return $json;
    }

    public function sms(array $body): mixed {
        return $this->request('sms', $body);
    }

    function pre_save(&$config, &$errors): bool {
        global $msg;

        [$__] = self::translate();
        $apiKey = &$config[self::KEY_API_KEY];
        $json = $this->request('balance', [], $apiKey);

        $invalidKeyErrorMessage = $__('The API key seems to be invalid.');
        if ($json === null) {
            $apiKey = '';
            $errors['err'] = $invalidKeyErrorMessage;

            return false;
        }

        if (!$errors) $msg = $__('Configuration updated successfully');

        return true;
    }
}
