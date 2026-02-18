# RDP Configuration Files

This directory contains RDP (Remote Desktop Protocol) configuration files and documentation.

## Files

- **RDP-RECONNECTION.md** - Comprehensive documentation about RDP auto-reconnection settings in both Dutch and English
- **verbinding-improved.rdp** - Improved RDP configuration file with optimal auto-reconnection settings

## Quick Start

1. Open `verbinding-improved.rdp` in a text editor
2. Replace `YOUR_SERVER_IP` with your actual server IP address
3. Replace `DOMAIN\username` with your domain and username
4. Save the file
5. Double-click the file to connect to your remote desktop

## What's New

The improved configuration includes:
- **Auto-reconnection enabled** - Automatically reconnects when connection is lost
- **20 retry attempts** - Tries up to 20 times to reconnect
- **Enhanced CredSSP support** - Better authentication after reconnection
- **Keep-alive signals** - Prevents timeout during inactivity (60 seconds)

## Documentation

See [RDP-RECONNECTION.md](RDP-RECONNECTION.md) for detailed documentation in Dutch and English.

## Security Note

Never commit RDP files with actual IP addresses, usernames, or sensitive information to version control. Always use placeholders or keep configured files locally.
