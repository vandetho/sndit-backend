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