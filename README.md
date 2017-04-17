[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rossmitchell/module-twofactor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rossmitchell/module-twofactor/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/rossmitchell/module-twofactor/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/rossmitchell/module-twofactor/?branch=master) [![Build Status](https://travis-ci.org/rossmitchell/module-twofactor.svg?branch=master)](https://travis-ci.org/rossmitchell/module-twofactor) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/841af9752a7c4fbabd53bce30b0f750a)](https://www.codacy.com/app/rossmitchell/module-twofactor?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=rossmitchell/module-twofactor&amp;utm_campaign=Badge_Grade)

Magento 2 Two Factor Authentication module
==========================================
 
This module will eventually provide Google Authenticator protection for both the customer and admin accounts of a 
Magento 2 store.

It will also give me a good excuse to play around with some of the different Magento 2 functionality and testing.

When this is ready, I'll send it across to packagist so it can be downloaded. 
 
For the moment, it is not even close to being complete or functional, so use entirely at your own risk
 
Current Status
--------------

From what I can tell this is now functionally complete, however it is entirely untested and undocumented.

With great thanks to Alan Storm and his [travis repo](https://github.com/astorm/magento2-travis) I have the repo set up 
to run tests, however none of these have been created yet.
 
The next task for this project is to add unit and integration tests to ensure the basic functionality works as expected,
and then refactor this to improve the quality of the code. 

I also need to setup some code coverage tests, so the badge at the top actually reports something
