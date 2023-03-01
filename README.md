# NextCaligo - Linux server manager
PhP-based remote Linux server &amp; virtual machine (VPS) management interface - thesis of Erik Stütz :tada:

## Functions
- Connect remote Linux servers (reboot, shutdown, change root password, change hostname, view resource statistics)
- Manage local server 
- Deploy and manage KVM virtual machines (start, stop, restart, create and restore backups, resize VM memory, delete)
- Account management (simple users, system administrators)
- The application logs all activity

![NextCaligo Server-manager](https://i.ibb.co/9bWHxCK/panel1.png)


## Installing & components
Components required for proper working:
- Apache2 (enable HTACCESS)
- php8.1-{cli, mysqli, gd, zip, mbstring, curl, libsodium, php-ssh2}
- Argon2ID implementation
- MariaDB/MySQL server
- Libvirt Tools & KVM
- SMTP mail server

Install components (Test phase: it overwrites everything):
```
sh installer/install_en.sh
```


## License
GNU General Public License v3.0 - It can be modified, distributed and used freely

** This project is my thesis, so it is not my life's work. My basic goal with it is to create a reference and explain my thinking to my future partners. However, if someone wants to develop it further, I am open to it.


## Used libraries & literature
I used open source libraries during development.
- Backend: Secureimage, TorUtils, PHPMailer
- Frontend: MDB4, Jquery 3, Bootstrap 4, FontAwesome 5 Free, ApexCharts, DataTables 1.13, ParticlesJS, "font-logos", "flag-icons" 

** I also indicated the frameworks, libraries and literature used for the project on the user interface of the application, thereby complying with the `open source` licenses.

> Thank you for reading!

`If you have any questions, suggestions or ideas, I look forward to hearing from you.`


