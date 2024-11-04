<img src="https://www.seven.io/wp-content/uploads/Logo.svg" width="250" />

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
3. Head to `Manage->Plugins`, click on `Add New Plugin` and install the `seven` plugin
4. Create a new plugin instance and enter your [API key](https://help.seven.io/en/api-key-access)
5. Don't forget to set the `Status` to `Active`

## Usage

### Send SMS

Open a ticket and click on the `More` button with the cog icon and click on the `Send SMS` item.
If the assignee has a mobile phone number set, the recipient field with be filled with its value.

### Event-based SMS dispatch
This plugin supports sending SMS after certain events have occurred.
Make sure to select a mail template first.

#### Send SMS after ticket creation
If enabled, an SMS gets sent to the assigned staff member or team lead.


## Support

Need help? Feel free to [contact us](https://www.seven.io/en/company/contact).

[![MIT](https://img.shields.io/badge/Licens
