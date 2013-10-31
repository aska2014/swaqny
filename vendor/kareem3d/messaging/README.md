# Messaging system
This class uses kareem3d membership system to create an easy usable messaging system

## Usage example

```php

// 1. Create message

$message = Message::create(array('subject' => ..., 'body' => ...));

$message->createdBy( Auth::user() );
$message->receivedBy( $user );
//-------------------------------------------------------------------------------------//


// 2. Get messages by time
$messages = Message::notSeenInbox(Auth::user()); // Not seen inbox messages for the authenticated user.
$messages = Message::inbox(Auth::user()); // All inbox messages
$messages = Message::sent(Auth::user()); // Sent messages
$messages = Message::trash(Auth::user()); //

// Move message
$message->moveToTrash( Auth::user() );

// Safe deletes the message
$message->moveToDeleted( Auth::user() );


$user = $message->getFromUser();
$user = $message->getToUser();
//-------------------------------------------------------------------------------------//
```