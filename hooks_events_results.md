## Hooks

Place these in your code. When a Hook and an Event share the key, the Event will run.

Full example:
```php
<?php
	$result = Hook::forge('thehookkey')
		->setObject($this)
		->setParam('theparamkey', $value)
		->setParams(array('anotherkey' => $anothervalue))
		->execute();

```

#### new Hook($key)

Instantiates a new hook.

* string  _$key_  The key on which Events will activate.

#### Hook::forge($key)

See: new Hook($key)

#### Hook::disable($key)

Disables a hook key.

* string  _$key_  The key to disable.

#### Hook::enable($key)

Enabled a disabled hook key.

* string  _$key_  The key to enable.

#### ->setObject($object)

Binds the object to the events. It's meant to be used with `$this`, but you can use it with any object.

* mixed  _$object_  The object to bind.

_Chainable_

#### ->setParam($key, $value)

Set a parameter for the Event.

* string  _$key_  The key for the value.
* mixed  _$key_  The value

_Chainable_

#### ->setParams($array)

Array version of `->setParam()`.

* array  _$array_  Associative array as $key => $value

_Chainable_

#### ->execute()

Runs the Events on the hook.

__Returns__  _\Foolz\Plugin\Result_  Filtered by the events.

