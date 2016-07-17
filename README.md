# Myesain\Strict

### Strict Object Property Access

PHP package to make accessing object properties more reliable - throws Exception when access to un-defined properties is attempted

Allows for more reliability by allowing data structures to be  type-hinted while not limiting PHP's flexibility

View the [changelog](CHANGELOG.md)

## Explanation

The `Myesain\Strict` library is an attempt to help prevent hard-to-identify issues *before* they have a chance to cause problems. This is accomplished by following a simple convention that enumerates an objects property names up front and allowing the library to prevent dynamic assignment of property names outside that list.

Consider normal PHP behavior:

```php
<?php
class BlogPost
{
    public $title;
    public $content;
    public $date;
}

$post = new BlogPost();
$post->title = "Very Engaging Title";
$post->cotnent = "[...] more engaging content [...]";
$post->date = "2016-07-16";
```

Our `BlogPost` class exposes 3 public properties, but when assigning values to those properties, the `content` property was misspelled. (Un)Fortunately, PHP's default behavior accomodates this, resulting in our `$post` being structured like this:

```php
BlogPost Object
(
    [title] => Very Engaging Title
    [content] =>
    [date] => 2016-07-16
    [cotnent] => [...] more engaging content [...]
)
```

Of course, in this contrived example, the problem will be spotted quickly (as soon as the body of the blog post doesn't appear on the screen), but in a more complex application, this issue may be harder to identify, finding it's way into production causing issues for users.

The `Myesain\Strict` library can help by allowing developers to specify the only properties that should be exposed. Any attempts to set a property outside that list will result in an `Exception` being thrown, alerting the developer prior to the code being deployed:

```php
<?php
class BlogPost
{
    use Myesain\Strict\StrictTrait;

    protected $properties = array(
        'title',
        'content',
        'date'
    );
}

$post = new BlogPost();
$post->title = "Very Engaging Title";

$post->cotnent = "[...] more engaging content [...]";
// Myesain\Strict\Exception\NonExistentPropertyException thrown: Property 'content' not a valid property of BlogPost
```

## Installation

Preferred method: install via composer:

`$ composer install myesain/strict-object`

then simply include `vendor/autoload.php` into your script.

## Usage

There are several ways to gain `Strict` functionality.

### `Myesain\Strict\StrictTrait`

The first, and indeed the cornerstone of the entire library, is by using the `Myesain\Strict\StrictTrait` and adding a `protected` array named `$properties` to your object (remove any `public` properties of your object and add the name to the `$properties` array:

```php
class User
{
    use Myesain\Strict\StrictTrait;

    protected $properties = array(
        'userId',
        'firstName',
        'lastName',
        'email',
        'isActive',
    );
}
```

Then use this object the same as you would have.

#### Assigning Values

Property values can be assigned using PHP's simple object accessor operator:

```php
$user = new User();

$user->userId = 726;
$user->firstName = 'Rasmus';
$user->lastName = 'Lerdorf';
$user->email = 'rasmus@php.net';
$user->isActive = '1';
```

The `Myesain\Strict\StrictTrait` also contains a `hydrate` method for assigning all values of your `Strict` objects from a PHP array:

```php
/* Assuming values contained in $userData array map to the properties of our object */

$user = new User();

$user->hydrate($userData);

```

Any `$userData` values that correspond to the entries in the `$properties` array of the `User` object will be properly populated.

Even though there is little difference in how our application can access the data for our `User` object (`$user->userId` vs `$_POST['userId']`), we've gained some very helpful capabilities.

#### Type-Hinting

Perhaps most importantly, we can type-hint against our `User` class now and be assured we can access properly named properties.

```php
class UserRepository
{
    public function saveUser(User $user)
    {
        [...]
    }
}
```

#### Data Privacy

We can also easily segregate attributes of our data array if we are performing two actions on the same request. For example, if a user can submit a shipping address and credit card information on the same request, we can easily prevent each of the storage processes from reaching out and affecting the other:

```php
class ShippingAddress
{
    use Myesain\Strict\StrictTrait;

	protected $properties = array(
    	'addressLine1',
        'addressLine2',
        'city',
        'state',
        'zip',
    );
}

class CreditCardData
{
    use Myesain\Strict\StrictTrait;

	protected $properties = array(
        'name',
        'cardNumber',
        'expiration',
        'CSV',
    );
}

$address = new ShippingAddress();
$address->hydrate($_POST);

$creditCard = new CreditCardData();
$creditCard->hydrate($_POST);

$addressRepository->saveAddress($address);

$orderProcessor->processCreditCardPayment($creditCard);
```
Here, the `$addressRepository` wouldn't be able to access the (arguably) more sensitive payment card information and the `$orderProcessor` won't be able to inadvertently affect the shipping address information, but we maintain a simple API into both of them.

### Extending `Myesain\Strict\StrictObject`

Secondly, your object can extend the `abstract` `Myesain\Strict\StrictObject` and gain 2 important features: constructor hydration and a `JsonSerializable` interface:

#### Constructor Hydration

The data that should hydrate the object can be provided as the argument to the constructor of objects that extend `Myesain\Strict\StrictObject`:

```php
class User extends \Myesain\Strict\StrictObject
{
	protected $properties = array(
        'userId',
        'firstName',
        'lastName',
        'email',
        'isActive',
    );
}

/* Use PDO to query User data from database */
$userData = $pdoStatement->fetch();
$user = new User($userData);
```

#### JsonSerializable

`Myesain\Strict\StrictObject` also implements `JsonSerializable` allowing objects to be processed by PHP's `json_encode` function, especially useful for APIs and microservices:

```php
$userData = $pdoStatement->fetch();
$user = new User($userData);

echo json_encode($user);
//{"userId":"726","firstName":"Rasmus","lastName":"Lerdorf","email":"rasmus@php.net","isActive":"1"}
```

### `Myesain\Strict\StrictArrayObject`

Finally, the `Myesain\Strict` provides `StrictArrayObject` allowing developers to utilize the functionality provided by the library (type-hinting, hydration, data privacy, json serializing, etc) while interacting with the construct as a native PHP array. `StrictArrayObject` implements `ArrayAccess`, `Countable`, and `IteratorAggregate` allowing for the following interactions:

```php
class Product extends \Myesain\Strict\StrictArrayObject
{
    $properties = array(
        'productId',
        'name',
        'upc',
        'cost',
    );
}

$book = new Product();
$book['productId'] = '1234';
$book['name'] = 'Computers Are Neat';
$book['upc'] = '1256-8897-6959';
$book['cost'] = 19.99;

$numProperties = count($book); // 4

foreach ($book as $key => $value) {
	// Perform some action using all properties of the object, e.g. composing database query
}
```

While the array access functionality may seem a bit contrived, there are several circumstances where this behavior may prove especially helpful.

#### Developers used to working with arrays

Old habits die hard, and if developers are excited about the features of the library but aren't interested in trading in their square brackets, this allows them to continue to code in their preferred style while taking advantage of the other features.

#### Refactoring

Existing code can be updated to take advantage of many features of the library without having to update all of the business logic surrounding it. For example, a method expecting an array of data as it's argument can be easily refactored to take advantage of the type-hinting and data-privacy features of the library.

First, create the new class:

```php
class Customer extends \Myesain\Strict\StrictArrayObject
{
    protected $properties = array(
        'name',
        'address',
        'city',
        'state',
        'zip',
        'gender',
        'email',
    );
}
```

Then update the places in the code where an array of customer data is being provided and add or change the type-hint:

```php
class CustomerRepository
{
    public function saveCustomer(array $customer)
    {
        [...]
    }
}
```

becomes

```php
class CustomerRepository
{
    public function saveCustomer(Customer $customer)
    {
        [...]
    }
}
```

Finally, update where the `saveCustomer` method is invoked to provide an instance of `Customer` instead:

```php
$customerRepository = [...];// Repository created with it's dependencies

$customerRepository->saveCustomer($_POST);
```

becomes

```php
$customerRepository = [...];// Repository created with it's dependencies

$customerRepository->saveCustomer(new Customer($_POST));
```

Implementing these three small steps have allowed us to type-hint more appropriately while also preventing the `CustomerRepository` from accidentally affecting other `$_POST` data. Also, because the `saveCustomer` expected an array and our new `Customer` class exhibits array-like behavior, that method doesn't have to be changed in order to provide these benefits.

## Get In Touch

I've come across a number of projects with similar functionality to what is provided here. I began working on the beginnings of this project prior to finding those though, and realized that my implementation covered a few situations that other projects did not.

Any comments or discussion are certainly welcome.

Of course, bug reports, new feature requests and pull requests are welcome as well.

### License

This project is proud to be licensed under the terms of The MIT Licensed (MIT). Please see the [license file](LICENSE) for more information.















