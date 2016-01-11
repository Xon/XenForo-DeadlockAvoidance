# XenForo-DeadlockAvoidance

Some hacks to avoid XenForo design issues which trigger DB errors.

XenForo's Datawriters have a _postSaveAfterTransaction() method. This method is intended to run after a database transaction is finished, but if a DataWriter is called from with-in another DataWriter,  this does not happen. 

This incurs the risk that various notification actions will pull in large queries into the transaction, which increasing the risk of deadlocks on a busy forum.

- Race condition for watch thread toggle
- Deadlock on conversations
- Deadlock on posts
- Deadlock on resolving/rejecting Reports

## Requirements
- php +5.5

## Features

### Race condition for watch thread toggle

Fixes a race condition when creating/removing a watch thread state

### Deadlock on conversations

Provides a workaround for a XenForo design issue where updating conversation counters can cause deadlocks.
- rebuilding user conversation counters occurs inside a large transaction, and is deadlock prone.

### Deadlock on posts

Provides a workaround for a XenForo design issue where adding/removing posts can cause deadlocks.
- updating user alert counters occurs inside a large transaction, and is deadlock prone.

### Deadlock on resolving/rejecting Reports
Provides a workaround for a XenForo design issue where adding/removing posts can cause deadlocks.
- Sends report resolve/reject alerts inside the transaction.
