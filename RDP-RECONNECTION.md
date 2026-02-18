# RDP Auto-Reconnection Configuratie / Configuration

## Nederlandse Uitleg

### Vraag: Kan ik hier iets in instellen dat deze de verbinding weer hersteld zodra deze weg is?

**Ja!** Je huidige configuratie heeft al `autoreconnection enabled:i:1` ingesteld, wat automatische herverbinding activeert. Echter, er zijn aanvullende instellingen die de herverbinding kunnen verbeteren:

### Belangrijke Instellingen voor Auto-Reconnection

1. **autoreconnection enabled:i:1** (Al ingesteld ✓)
   - Dit activeert de automatische herverbindingsfunctie
   - Waarde: 1 = ingeschakeld, 0 = uitgeschakeld

2. **Aanbevolen Toevoegingen:**

```
autoreconnect max retries:i:20
```
- Bepaalt hoeveel keer RDP probeert opnieuw verbinding te maken
- Standaard: 20 pogingen
- Hogere waarde = meer pogingen bij verbindingsproblemen

```
enablecredsspsupport:i:1
```
- Verbetert authenticatie na herverbinding
- Waarde: 1 = ingeschakeld

```
keep alive interval:i:60000
```
- Houdt de verbinding actief door regelmatig signalen te sturen
- Waarde in milliseconden (60000 = 60 seconden)

### Optimale Configuratie

Voeg deze regels toe aan je .rdp bestand voor de beste herverbindingservaring:

```
autoreconnection enabled:i:1
autoreconnect max retries:i:20
enablecredsspsupport:i:1
keep alive interval:i:60000
```

### Wat gebeurt er bij verbindingsverlies?

1. De RDP-client detecteert het verbindingsverlies
2. Automatisch wordt geprobeerd opnieuw verbinding te maken
3. Tot 20 keer (of het aantal dat je hebt ingesteld) wordt geprobeerd
4. Als de verbinding hersteld wordt, ga je verder waar je was gebleven

---

## English Explanation

### Question: Can I configure something here to restore the connection when it's lost?

**Yes!** Your current configuration already has `autoreconnection enabled:i:1` set, which enables automatic reconnection. However, there are additional settings that can improve reconnection:

### Important Auto-Reconnection Settings

1. **autoreconnection enabled:i:1** (Already set ✓)
   - Enables the automatic reconnection feature
   - Value: 1 = enabled, 0 = disabled

2. **Recommended Additions:**

```
autoreconnect max retries:i:20
```
- Determines how many times RDP attempts to reconnect
- Default: 20 attempts
- Higher value = more attempts during connection issues

```
enablecredsspsupport:i:1
```
- Improves authentication after reconnection
- Value: 1 = enabled

```
keep alive interval:i:60000
```
- Keeps the connection alive by sending regular signals
- Value in milliseconds (60000 = 60 seconds)

### Optimal Configuration

Add these lines to your .rdp file for the best reconnection experience:

```
autoreconnection enabled:i:1
autoreconnect max retries:i:20
enablecredsspsupport:i:1
keep alive interval:i:60000
```

### What happens when connection is lost?

1. The RDP client detects the connection loss
2. Automatically attempts to reconnect
3. Retries up to 20 times (or the number you've configured)
4. If connection is restored, you continue where you left off

---

## Complete Improved RDP Configuration File

Save this as `verbinding.rdp` or modify your existing file:

```rdp
screen mode id:i:2
use multimon:i:0
desktopwidth:i:1920
desktopheight:i:1080
session bpp:i:32
winposstr:s:0,3,0,0,800,600
compression:i:1
keyboardhook:i:2
audiocapturemode:i:0
videoplaybackmode:i:1
connection type:i:7
networkautodetect:i:1
bandwidthautodetect:i:1
displayconnectionbar:i:1
enableworkspacereconnect:i:0
disable wallpaper:i:0
allow font smoothing:i:0
allow desktop composition:i:0
disable full window drag:i:1
disable menu anims:i:1
disable themes:i:0
disable cursor setting:i:0
bitmapcachepersistenable:i:1
full address:s:192.168.241.11
audiomode:i:0
redirectprinters:i:1
redirectcomports:i:0
redirectsmartcards:i:1
redirectclipboard:i:1
redirectposdevices:i:0
autoreconnection enabled:i:1
autoreconnect max retries:i:20
authentication level:i:2
prompt for credentials:i:1
negotiate security layer:i:1
remoteapplicationmode:i:0
alternate shell:s:
shell working directory:s:
gatewayhostname:s:
gatewayusagemethod:i:4
gatewaycredentialssource:i:4
gatewayprofileusagemethod:i:0
promptcredentialonce:i:0
gatewaybrokeringtype:i:0
use redirection server name:i:0
rdgiskdcproxy:i:0
kdcproxyname:s:
username:s:uitvaartndal\
remoteappmousemoveinject:i:1
redirectlocation:i:0
redirectwebauthn:i:1
drivestoredirect:s:
enablerdsaadauth:i:0
enablecredsspsupport:i:1
keep alive interval:i:60000
```

### Key Improvements:
- ✅ Automatic reconnection enabled
- ✅ Maximum 20 retry attempts
- ✅ Enhanced CredSSP support
- ✅ Keep-alive interval to maintain connection

### Notes:
- The `keep alive interval` setting helps prevent timeouts during periods of inactivity
- If you experience frequent disconnections, check your network stability
- Consider adjusting `autoreconnect max retries` based on your network reliability
