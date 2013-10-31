# Membership based on laravel eloquent ORM
These two classes use kareem3d extension of laravel eloquent ORM to build a membership system easy to use
in all website applications

You get to work with two classes. User class and UserInfo class. User class represents a user with an account
meaning the user has email, password and optionally a username.

UserInfo represents any information about user. This could be attached to user(account) or not to be attached
to anything at all.

These both classes created separately because there are a lot of situations where you don't need a user account
but still need his information e.g. Create a comment by user name and website only (No need for account).

## Usage example

```php

// 1. Create user account

// Create user account (username, email and password -- don't worry about hashing --).
$user = new User(array('username' => .., 'email' => ..., 'password' => 'kareem3d'));

// Create user information
$userInfo = new UserInfo(array('name' => 'Kareem Mohamed', 'website' => ....));

if($user->isValid() and $userInfo->isValid()) {

    $user->save();

    $user->setInfo($userInfo);

    // User account created successfully
    ....
}
else {

    // Merge both user information and user validator messages
    $errors = $user->getValidatorMessages()->merge($userInfo->getValidatorMessages()->toArray());

    // Redirect back with errors
    return Redirect::back()->withErrors($errors);
}
//-------------------------------------------------------------------------------------//





// 2. Recipients and creations
// An easy way to manage creations and recipients for your site users using polymorphic relationships.

$message = Message::create(array('subject' => ...., 'body' => ....));
$user1->creates($message);
$user2->receives($message); // That's it!

// Later in user1 sent box
// Get all messages created by this user
$messages = $user1->getCreations('Message'); // Suppose Message class is in the global namespace

// Get all messages received by this user
$messages = $user2->getRecipients('Message');

$user1->removeCreation($message);
$user2->removeRecipient($message);

// And more....
$users = User::getByRecipient($message) // Get all users received this message
$users = User::getByCreation($message) // Get all users created this message

if($user1->hasCreated($message)) { ... }
if($user2->hasReceived($message)) { ... }

$messages = $user2->getNotSeenRecipients('Message');
$messages = $user2->getSeenRecipients('Message');
$user2->seenRecipient($message); // Seen this particular message
$user2->seenRecipients('Message'); // Seen all messages

// Adding extra information within recipient or creation row...
$user1->creates($message, array('type' => 'active'));
$user2->setRecipientExtra($message, array('type' => 'trash')); // Move to trash
//-------------------------------------------------------------------------------------//





// 3. Online users
$user->makeOnline(); // Do this in every request to update his online state

if($user->isOnline()) { ... }

echo $user->getOnlineAt() // Get last time this user was online at.

// Also in the user class you can set REQUEST_ONLINE_SECONDS constant to how much seconds you call
// makeOnline method on user
//-------------------------------------------------------------------------------------//
```