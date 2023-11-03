# Activate HTTP3 (Quic) OR HTTP2 on Nginx:

In this section you will know how to activate the h2 or h3 protocol on nginx:

## Prerequisites
 - A domain name
 - A server run on nginx
 - A SSL certificate on nginx (HTTPS)
 - Nginx version 1.25.0 or later installed
 - OpenSSL version 1.1.1 or later installed
 - Basic knowledge of Linux command line and Nginx configuration

Add the following code to your server conf:
```apacheconf
    listen 443 ssl;
    listen 443 ssl quic reuseport;
    http2 on;
```

If you use a firewall like ufw, it is also necessary to allow UDP which is used by QUIC protocol.
```
sudo ufw allow 80/udp
sudo ufw allow 443/udp
sudo ufw reload
```

Check if the port is allow:
```
sudo ufw status
Status: active

To                         Action      From
--                         ------      ----
Nginx Full                 ALLOW       Anywhere
SSH                        ALLOW       Anywhere
OpenSSH                    ALLOW       Anywhere
22                         ALLOW       Anywhere
80/udp                     ALLOW       Anywhere
443/udp                    ALLOW       Anywhere
Nginx Full (v6)            ALLOW       Anywhere (v6)
SSH (v6)                   ALLOW       Anywhere (v6)
OpenSSH (v6)               ALLOW       Anywhere (v6)
22 (v6)                    ALLOW       Anywhere (v6)
80/udp (v6)                ALLOW       Anywhere (v6)
443/udp (v6)               ALLOW       Anywhere (v6)
```

To check if your website is run on Quic
https://http3check.net/