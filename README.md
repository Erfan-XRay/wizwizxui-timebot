<p align="center">
  <a href="https://github.com/Erfan-XRay/wizwizxui-timebot" target="_blank" rel="noopener noreferrer">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://user-images.githubusercontent.com/27927279/227711552-d2bc1089-5666-477b-9be7-d7e50a5286dc.png">
      <img width="200" height="200" src="https://user-images.githubusercontent.com/27927279/227711552-d2bc1089-5666-477b-9be7-d7e50a5286dc.png">
    </picture>
  </a>
</p> 

<p align="center">
	<a href="./README.md">
	English
	</a>
	/
	<a href="./README-fa.md">
	فارسی
	</a>
</p>

<h1 align="center">WizWiz XUI TimeBot</h1>

<p align="center">
Easy to sell with <a href="https://github.com/Erfan-XRay/wizwizxui-timebot">wizwizxui-timebot</a> - easy install with one command
</p>

<p align="center">
WizWiz is a powerful and professional bot designed for managing and selling VPN services. This version exclusively supports the <strong>Sanaei XUI Panel</strong> and is the best option for selling. It supports most protocols and has easy installation.
</p>

<div align=center>

[![GitHub](https://img.shields.io/github/license/Erfan-XRay/wizwizxui-timebot?style=flat-square)](https://github.com/Erfan-XRay/wizwizxui-timebot)
[![GitHub release](https://img.shields.io/github/v/release/Erfan-XRay/wizwizxui-timebot.svg)](https://github.com/Erfan-XRay/wizwizxui-timebot/releases)

</div>

<br>
<br>
    <a align="center">
        <img src="https://github.com/wizwizdev/wizwizxui-timebot/assets/27927279/f6635ea5-ab26-4c64-a7b8-952203f79763" />
    </a>     
<br>
<br>

## 📋 Prerequisites

- Ubuntu 20.04 server or higher
- Root access
- Domain with DNS configured
- **Sanaei XUI** panel installed (latest version)
- Telegram bot from @BotFather

## 🚀 Installation on Ubuntu 20.04

> ⚠️ **Note:** This version only supports **Sanaei XUI** panel

- If your server does not have root access, please grant root access with `sudo -i` command and then install
- Create a bot in @botfather and /start it
- Before installation, make sure to set the server IP for the domain

### Automatic Installation:

```bash
bash <(curl -s https://raw.githubusercontent.com/Erfan-XRay/wizwizxui-timebot/main/install.sh)
```

or:

```bash
bash <(curl -s https://raw.githubusercontent.com/Erfan-XRay/wizwizxui-timebot/main/wizwiz.sh)
```

### Installation Steps:

1. First enter "sub.domain.com" or "domain.com" without https
2. Enter email
3. Enter `y`
4. Enter `2`
5. Enter username for database
6. Enter password for database
7. Enter bot token
8. Enter Numerical ID of admin from @userinfobot
9. Re-enter "sub.domain.com" or "domain.com" without https

✅ Very good, the installation message ( ✅ The wizwiz bot has been successfully installed! ) is sent to the bot

<br>
<br>

## 🔄 Update bot - Update panel - backup - remove wizwiz

- With every update and backup, a notification is sent to the manager robot

```bash
bash <(curl -s https://raw.githubusercontent.com/Erfan-XRay/wizwizxui-timebot/main/update.sh)
```

<br>

<hr>

<br>

## 🎯 Supported Panel

This version **only** supports **Sanaei XUI** panel:

### Install Sanaei XUI Panel:

```bash
bash <(curl -Ls https://raw.githubusercontent.com/mhsanaei/3x-ui/master/install.sh) v1.7.9
```

> ⚠️ **Important:** Please update your Sanaei XUI panel to the latest version to support the new API.

<br>
<hr>
<br>

## 💰 Donation

- Tron (TRX): `TY8j7of18gbMtneB8bbL7SZk5gcntQEemG`
- Bitcoin: `bc1qcnkjnqvs7kyxvlfrns8t4ely7x85dhvz5gqge4`
- Dogecoin: `DMyGMghEh4W55P3VeVHntCN3vYAFtshvVH`

<br>
<hr>
<br>

## ✨ Features

- nowpayments - zarinpal - nextpay portal and rial currency
- Support for - xtls - tls - reality - Grpc - ws - tcp
- vless - vmess - trojan support
- The possibility of extending the service
- Display the configuration profile as a sub (in the v2ray software)
- Representation (individual and mass purchase - configuration management - sales statistics, etc.)
- Ability to search purchased configurations for easy access (representative)
- View configuration information on the web (modern and responsive UI)
- Button to register the link to renew and update the config (not purchased from bot)
- Config QRcode button
- Delete configuration by user (delete from x-ui panel and database + delete notification)
- Ability to add volume and time on desired server + notification
- Ability to deduct balance from user credit
- Ability to disconnect and receive a new link by the user (change uuid)
- Ability to update the connection (according to your changes in the panel)
- The ability to change the config name (random and normal)
- Determining the configuration name when purchasing by the user (custom plan)
- Smart subscription
- Filtering status of servers
- Automatic relocation
- Increasing volume and service time
- ability to pass
- The possibility of ordering the desired design by the user
- Authentication of Iranian and foreign contact numbers
- Backup x-ui panel
- Subcommittee and commission
- Create discount and gift codes
- Ability to track the user
- Create button and answer for it
- Configuration output with different IP or domains
- Ability to change protocol and network type
- Set configuration port randomly or automatically
- Wallet (possibility of charging - balance transfer)
- Send notification of new member in robot to (admin)
- Display user information (user-admin)
- Ability to send private messages from the admin to the user
- Ability to manage and view servers - categories - plans
- Ability to block and release
- Ability to add admin
- Show the inventory of servers
- Ability to send income reports to the channel
- Send public messages
- Get sold configurations
- Create a shared port and configure a dedicated port
- Account testing for users
- Card to card functionality
- Display the sold accounts of each plan
- Display capability (software link)
- Send public messages with CronJob
- Announcing the end of volume and configuration time (to the user)
- Forced channel lock
- Ability to get link details
- Off/on capability (all robot features)
- Notification of purchase information + renewal etc. in full to the admin robot

<br>
<hr>
<br>

## 🔧 Changes in This Version

This version is updated and maintained by **ErfanXRay**:

- ✅ **Sanaei XUI Only:** Removed support for other panels (Marzban, Alireza, Niduka, Vaxilu)
- ✅ **Simplified Server Addition:** Only 4 steps (name, flag, panel URL, username and password)
- ✅ **Improved Plan Creation:** Select Inbound from server list instead of manual entry
- ✅ **New API Support:** Compatible with latest Sanaei XUI version
- ✅ **Modern UI:** Modern and responsive web interface for user information display

<br>
<hr>
<br>

## 📞 Contact Developer

💎 GitHub: https://github.com/Erfan-XRay/wizwizxui-timebot

<br>
<br>

## 📝 License

This project is released under the MIT License.
