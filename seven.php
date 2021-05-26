<?php /** @noinspection PhpUnused */

require_once INCLUDE_DIR . 'class.plugin.php';
require_once 'config.php';

class SevenPlugin extends Plugin {
    /**
     * The name that appears in threads as: Closer Plugin.
     * @var string
     */
    public const PLUGIN_NAME = 'seven Plugin';
    var $config_class = 'SevenPluginConfig';

    private function getInstanceConfig(): SevenPluginConfig {
        /** @var Plugin $plugin */
        $plugin = Plugin::lookup(['name' => 'seven']);
        /** @var QuerySet $activeInstances */
        $activeInstances = $plugin->getActiveInstances();
        /** @var PluginInstance $activeInstance */
        $activeInstance = $activeInstances->first();
        /** @var SevenPluginConfig $config */
        $config = $activeInstance->getConfig();

        return $config;
    }

    private function getStaffMobilePhone(int $staffId): string {
        $staff = Staff::lookup($staffId);
        $info = $staff->getInfo();
        return is_array($info) ? $info['mobile'] ?? '' : '';
    }

    private function getStaffId(QuerySet $querySet): ?int {
        $data = $querySet->values_flat('staff_id')->first();
        return $data[0] ?? null;
    }

    public function bootstrap() {
        Signal::connect('ticket.view.more', function ($ticket) {
            global $thisstaff;

            if (!$thisstaff || !$thisstaff->isAdmin()) return;

            if (!$this->getInstanceConfig()->getApiKey()) return;

            $url = 'ajax.php/seven/sms/' . $ticket->getId() . '/view';
            printf('<li><a onclick="javascript: $.dialog(\'%s\', 201); return false;"', $url);
            echo '><i class="icon-envelope"></i>' . __(' Send SMS') . '</a></li>';
        });

        Signal::connect('ajax.scp', function ($dispatcher) {
            $dispatcher->append(
                url_get('^/seven/sms/(?P<id>\d+)/view$', function ($ticketId) {
                    global $thisstaff;

                    if (!$thisstaff || !$thisstaff->isAdmin())
                        Http::response(403, 'Contact your administrator');

                    $row = Ticket::objects()->filter(['ticket_id' => $ticketId]);

                    if (!$row) Http::response(404, 'No such ticket');

                    $staffId = $this->getStaffId($row);
                    $recipient = $staffId ? $this->getStaffMobilePhone($staffId) : '';
                    $apiKey = $this->getInstanceConfig()->getApiKey();

                    include 'templates/sms.tmpl.php';
                })
            );
        });
    }

    function uninstall(&$errors) {
        global $ost;

        $ost->alertAdmin(self::PLUGIN_NAME . ' has been uninstalled',
            'You wanted that right?', true);

        $errors = [];
        parent::uninstall($errors);
    }
}
