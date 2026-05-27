# Dovecot

[Dovecot](https://www.dovecot.org/) is an open-source IMAP and POP3 server.

!!! note
    This integration requires **Dovecot 2.4** or later. The statistics
    system was redesigned in 2.4 and the metric definitions below are
    not compatible with earlier versions.

## Dovecot configuration

Dovecot 2.4 requires metrics to be explicitly defined before
`doveadm stats dump` will return data. Add the following to your
Dovecot configuration (e.g. `/etc/dovecot/conf.d/10-stats.conf`):

```
##
## Statistics and metrics
##
## In Dovecot 2.4, metrics must be explicitly defined for
## doveadm stats dump to return data.
##

metric auth_success {
  filter = event=auth_request_finished AND success=yes
}

metric auth_failures {
  filter = event=auth_request_finished AND NOT success=yes
}

metric imap_command {
  filter = event=imap_command_finished
}

metric smtp_command {
  filter = event=smtp_server_command_finished
}

metric sieve_action {
  filter = event=sieve_action_finished
}

metric mail_delivery {
  filter = event=mail_delivery_finished
}
```

Reload Dovecot after making changes:

```bash
doveadm reload
```

Verify that statistics are being collected:

```bash
doveadm stats dump
```

## SNMP Extend

=== "SNMP Extend"

    1. Install the collection script to the target host:

        ```bash
        wget https://raw.githubusercontent.com/librenms/librenms-agent/master/snmp/dovecot-stats-snmp.sh \
            -O /usr/local/bin/dovecot-stats-snmp.sh
        ```

    2. Make the script executable:

        ```bash
        chmod +x /usr/local/bin/dovecot-stats-snmp.sh
        ```

    3. The script calls `doveadm stats dump` and `doveadm who`. The
    `snmpd` daemon typically runs as an unprivileged user and may not
    have permission to run `doveadm`. Grant access via sudo:

        ```bash
        # /etc/sudoers.d/snmpd-dovecot
        Debian-snmp ALL = NOPASSWD: /usr/bin/doveadm
        ```

        Adjust the username to match the user `snmpd` runs as on your
        system (e.g. `snmpd` on RHEL/Rocky, `Debian-snmp` on
        Debian/Ubuntu). Then update the script to use sudo:

        ```bash
        # At the top of /usr/local/bin/dovecot-stats-snmp.sh, set:
        DOVEADM="sudo /usr/bin/doveadm"
        ```

    4. Edit your snmpd configuration (usually `/etc/snmp/snmpd.conf`)
    and add:

        ```bash
        extend dovecot /usr/local/bin/dovecot-stats-snmp.sh
        ```

    5. Restart snmpd:

        === "Systemd"

            ```bash
            sudo systemctl restart snmpd
            ```

        === "Xinetd"

            ```bash
            sudo service snmpd restart
            ```

    6. Verify the extend is working:

        ```bash
        snmpget -v2c -c <community> <host> \
            'NET-SNMP-EXTEND-MIB::nsExtendOutputFull."dovecot"'
        ```

        You should see a JSON array containing the collected metrics.

    The application should be auto-discovered the next time LibreNMS
    runs discovery against the host. If it is not, follow the steps
    under the *Enable the application(s) to be discovered* section at
    the top of the [Applications](../Applications.md) page.

## Graphs

The following graphs are available once the application is discovered
and polled:

| Graph | Description |
|-------|-------------|
| Connected Users & Sessions | Current number of connected users and active sessions |
| Authentication (rate) | Rate of successful and failed authentication attempts |
| Authentication Duration | Auth response times — avg, median, 95th percentile (µs) |
| IMAP Commands | IMAP command throughput and latency |
| SMTP Commands | SMTP command throughput and latency |
| Mail Delivery | Mail delivery throughput and latency |
| Sieve Actions | Sieve script action throughput and latency |

All duration values are in **microseconds**.
