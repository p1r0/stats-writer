Stats
========

Intro
--------

Stats is a small php project to help using stats from your current project.
It supports any backend for which you want to write an adapter and it comes with one for
statsd.

Installing
-----------

No actual installation is needed just add this to your composer.json require section:

<pre>
	"heapstersoft/stats-writer": "1.0.x-dev"
</pre>

Usage
--------

First of all you need to include your composer autoload as with any project using composer:

<pre>
	require 'vendor/autoload.php';
</pre>

ideally you would do this in one central place and only once.

Then to send a stat to the backend (see configuration section) you need to create a stats object
passing the config file as a first paramenter and the just use either increment or decrement:

<pre>
	$statWriter = new \Heapstersoft\Stats\Writer('config/stats.yml');

	$statWriter->increment('key1');
</pre>

For more information I encourage you to view the source code.

Congfiguration
----------------

Stats uses a simple YAML file as configuration with a mandatory "Adapter" key.

Under adapter you just neet the adapter class. The rest of the parameters ara adapter specific.

For an example configuration see the StatsD adapter section.

Adapters
-----------

### StatsD

Example configuration file for StatsD:

<pre>
	Adapter:
	  class: \Heapstersoft\Stats\Adapter\StatsD
	  host: "127.0.0.1"
	  port: 8125
	  key: "#host#.test.#key#"
</pre>

All parameter should be self explainatory except for the "key" parameter.

Key allows you to customize the key string sent to the statsd backend. It suports any string
with some special placeholders.

Right now these placeholders are: #host# that is replaced by the $_SERVER['HTTP_HOST'] variable and the #key#
that is replaced for the value you pass to increment or decremant.

If no #key# is spcecified, it is appended.