FROM ianblenke/simplesamlphp:1.13.2

#RUN  sudo apt-get update && sudo apt-get install -y gettext 

ADD entrypoint.sh /
RUN chmod +x /entrypoint.sh

# Templates
RUN mkdir /templates
ADD apache2.conf /templates/

RUN cp -r /var/www/html/ /var/simplesamlphp/
RUN mkdir /var/simplesamlphp/cert
ADD config.php /var/simplesamlphp/config-templates/

# Simplesamlphp setup - idp
ADD authsources.php /var/simplesamlphp/config-templates/
ADD saml20-idp-hosted.php /var/simplesamlphp/metadata-templates/

RUN touch /var/simplesamlphp/modules/consent/enable
ADD modules/consent/ConsentService.php /var/simplesamlphp/modules/consent/lib/Consent/Store
ADD modules/consent/Consent.php /var/simplesamlphp/modules/consent/lib/Auth/Process
ADD modules/consent/Store.php /var/simplesamlphp/modules/consent/lib
ADD modules/consent/getconsent.php /var/simplesamlphp/modules/consent/www
ADD modules/consent/getconsenttemplate.php /var/simplesamlphp/modules/consent/www
ADD modules/consent/consentform.php /var/simplesamlphp/modules/consent/templates
ADD modules/consent/noconsent.php /var/simplesamlphp/modules/consent/templates
ADD modules/consent/logout_completed.php /var/simplesamlphp/modules/consent/templates
ADD modules/consent/consent.definition.json /var/simplesamlphp/modules/consent/dictionaries
ADD modules/consent/jquery-1.11.3.min.js /var/simplesamlphp/modules/consent/www

ADD modules/consent/pdfjs /var/simplesamlphp/modules/consent/www/pdfjs


ADD Logger.php /var/simplesamlphp/lib/SimpleSAML/
ADD LoggingHandlerSystemOut.php /var/simplesamlphp/lib/SimpleSAML/Logger/

ADD vendor/simplesamlphp/saml2/src/SAML2/AuthnRequest.php /var/simplesamlphp/vendor/simplesamlphp/saml2/src/SAML2/AuthnRequest.php

ENTRYPOINT ["/entrypoint.sh"]
