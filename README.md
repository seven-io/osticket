![](https://www.seven.io/wp-content/uploads/Logo.svg "seven Logo")

# Official [seven](https://www.seven.io) extension for [osTicket](https://osticket.com/)

Send SMS from within issues via [seven](https://www.seven.io).

## Prerequisites

- An [API key](https://help.seven.io/en/api-key-access) from [seven](https://www.seven.io) - can be
  created
  in your [developer dashboard](https://app.seven.io/developer)
- [osTicket](https://osticket.com/) - tested with v1.17.2

## Installation

1. Download
   the [latest release](https://github.com/seven-io/osticket/releases/latest/download/seven-osticket-latest.zip)
2. Extract the archive to `/path/to/osticket/include/plugins/`
3. Head to `Manage->Plugins` and enable the `seven` plugin
4. Create a new plugin instance and enter your [API key](https://help.seven.io/en/api-key-access)

## Usage

### Send SMS

Open a ticket and click on the `More` button with the cog icon and click on the `Send SMS` item.
If the assignee has a mobile phone number set, the recipient field with be filled with its value.

## Support

Need help? Feel free to [contact us](https://www.seven.io/en/company/contact).

[![MIT](https://img.shields.io/badge/License-MIT-teal.svg)](LICENSE)
