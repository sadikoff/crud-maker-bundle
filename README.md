koff/crud-maker-bundle
======================

[![Build Status](https://travis-ci.org/sadikoff/crud-maker-bundle.svg?branch=master)](https://travis-ci.org/sadikoff/crud-maker-bundle)
[![Latest Stable Version](https://poser.pugx.org/koff/crud-maker-bundle/v/stable.svg?format=flat-square)](https://packagist.org/packages/koff/crud-maker-bundle) 
[![Total Downloads](https://poser.pugx.org/koff/crud-maker-bundle/downloads.svg?format=flat-square)](https://packagist.org/packages/koff/crud-maker-bundle) 
[![Latest Unstable Version](https://poser.pugx.org/koff/crud-maker-bundle/v/unstable.svg?format=flat-square)](https://packagist.org/packages/koff/crud-maker-bundle) 
[![License](https://poser.pugx.org/koff/crud-maker-bundle/license.svg?format=flat-square)](https://packagist.org/packages/koff/crud-maker-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sadikoff/crud-maker-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sadikoff/crud-maker-bundle/?branch=master)
[![StyleCI](https://styleci.io/repos/118423208/shield?branch=master)](https://styleci.io/repos/118423208)
[![Coverage Status](https://coveralls.io/repos/github/sadikoff/crud-maker-bundle/badge.svg?branch=master)](https://coveralls.io/github/sadikoff/crud-maker-bundle?branch=master)

This bundle is extension to `symfony/maker-bundle`. It allows you to create simple crud with just one command.

Requirements
------------
* Symfony flex with Symfony 3.4|4.0
* symfony/maker-bundle

Installation
------------

    composer req koff/crud-maker-bundle

Configuration
-------------

No additional configuration needed

Usage
-----

    bin\console make:crud <Entity name>

or with iteraction

    bin\console make:crud


> Entity must exist before command use

License
=======
This package is available under the MIT license.