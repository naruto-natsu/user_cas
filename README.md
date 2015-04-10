INTRODUCTION
============

This App provide CAS authentication support, using the phpCAS library of Jasig.


INSTALLATION
============

PREVIOUS DEPENDENCE
-------------------

This App require the phpCAS library of Jasig version 1.3.2 or more. To learn how to install it on your system check:

* `phpCAS site <https://wiki.jasig.org/display/CASC/phpcas>`_


STEPS
-----

1. Install phpCAS
2. Copy the 'user_cas' folder inside the ownCloud's apps folder and give to apache server privileges on whole the folder.
3. Access to ownCloud web with an user with admin privileges.
4. Access to the Appications pannel and enable the CAS app.
5. Access to the Administration pannel and configure the CAS app.
6. Modify your web configuration 
        RewriteEngine On
        RewriteRule ^/$ /index.php?app=user_cas [L,R]



EXTRA INFO
----------
