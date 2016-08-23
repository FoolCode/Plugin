## Hook

Place these in your code. When a Hook and an Event share the key, the Event will run.

Full example:
```php
<?php
	$result = Hook::forge('the_hook_key')
		->setObject($this)
		->setParam('the_param_key', $value)
		->setParams(array('another_key' => $another_value))
		->execute();

```

#### new Hook($key)

Instantiates a new hook.

* string  _$key_ - The key on which Events will activate.

#### Hook::forge($key)

See: new Hook($key)

_Chainable_

#### Hook::disable($key)

Disables a hook key.

* string _$key_ - The key to disable.

#### Hook::enable($key)

Enabled a disabled hook key.

* string _$key_ - The key to enable.

#### ->setObject($object)

Binds the object to the events. It's meant to be used with `$this`, but you can use it with any object.

* mixed _$object_ - The object to bind.

_Chainable_

#### ->setParam($key, $value)

Set a parameter for the Event.

* string _$key_ - The key for the value.
* mixed _$key_ - The value

_Chainable_

#### ->setParams($array)

Array version of `->setParam()`.

* array _$array_ - Associative array as $key => $value

_Chainable_

#### ->execute()

Runs the Events on the hook.

__Returns:__ _\Foolz\Plugin\Result_ - A Result object filtered by the events.


----


## Event

When set, once a Hook with the same key is found, this will be run.

Full examples:

```php
<?php
Event::forge('the_hook_key')
	->setCall(function($result){

	})
	->setPriority(3);

Event::forge('another_hook_key')
	->setCall('Foolz\Theme\Plugin::rainbows')
	->setPriority(8);
```

###### Note on priority number

The higher the number, the lower the priority. You can go negative to achieve a higher priority. The hooks with lower priority will run later than the ones with higher priority. This also means that events with lower priority will edit the result after the high priority ones, thus modifying their results.

The default priority is 5.

#### new Event($key)

Instantiates a new hook.

* string  _$key_ - The Hook key on which the Event will activate.

#### Event::forge($key)

See: new Event($key)

_Chainable_

#### Event::clear($key)

Removes all the Events bound to a key up to now

* string _$key_ - The Hook key to search for

#### Event::getByKey($key)

Returns an array of the Events with the $key

* string _$key_ - The Hook key to search for

__Returns:__ _array_ - An array of events in decreasing order of priority

#### ->getPriority()

Returns the current priority for the Event.

__Returns:__ _int_ - The priority number

#### ->setPriority($priority)

Sets the new priority.

* int _$key_ - The new priority number

_Chainable_

#### ->getCall()

Returns the closure or string to the static method.

__Returns:__ _string|Callable_ - The closure or string to static method

#### ->setCall($callable)

Sets the Closure or string to static method to call

* string|Callable _$callable_ - Closure or string to static method to call

__PHP 5.4 Enhancement:__ If on the Hook you used `->setObject($object)`, Closures will have the object set to `$this` and be in the object's scope.


----


## Result

The result is an object to modify during the Events. The Hook will return the Result filtered by the Events.

Full example:
```php
<?php
	Event::forge('yet_another_hook')
		->setCall(function($result){
			$this_param = $result->getParam('this_param');
			$result->setParam('another_param', 'You are not here anymore.')
			$result->set($this_param);
		});

	$result = Hook::forge('yet_another_hook')
		->setParam('this_param', 'Nothing to see here.')
		->setParam('another_param', 'I am still here.')
		->execute();

	// echoes "Nothing to see here."
	echo $result->get('Your fallback');
	// echoes "You are not here anymore."
	echo $result->getParam('another_param');
	// uses the original parameter, and echoes "I am still here."
	echo $result->getParam('another_param', true);
```

Don't get fooled by the order. Hook creates `$result` first, passes it to the Event. The Event modifies the `$result`. It then goes back to the Hook, that returns it to you.

#### ->get($fallback = \Foolz\Plugin\Void)

Returns the result.

__Returns:__ _mixed_ - The result that has been set. The fallback if no result has been set. \Foolz\Plugin\Void if no result or fallback has been set.

__Notice:__ Void is useful to recognize `null` as a valid result from an Event.

#### ->set($val)

Sets the result.

* mixed _$val_ - Any value to be set as result

_Chainable_

#### ->getParam($key, $orig = false)

Gets the parameter.

* int|string _$key_ - The key that was given to the parameter
* bool _$orig_ - If true returns the first assigned value of the parameter

#### ->setParam($key, $value)

Sets a parameter.

* int|string _$key_ - The key to give to the value
* mixed _$value_ - The value to assign to the parameter

_Chainable_

#### ->getParams($orig = false)

Returns the array of parameters.

* bool _$orig_ - If true returns the first assigned values of the parameters

#### ->setParams($array)

Sets several parameters.

* array _$array_ - An array of $key => $value

_Chainable_
