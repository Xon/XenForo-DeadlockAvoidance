# XenForo-DeadlockAvoidance

Some hacks to avoid XenForo design issues which trigger DB errors

- Watch thread toggle
- Deadlock on conversations
- Deadlock on posts

### Watch thread toggle

Fixes a race condition when creating/removing a watch thread state

### Deadlock on conversations

Provides a workaround for a XenForo design issue where updating conversation counters can cause deadlocks.
- rebuilding user conversation counters occurs inside a large transaction, and is deadlock prone.

### Deadlock on posts

Provides a workaround for a XenForo design issue where adding/removing posts can cause deadlocks.
- updating user alert counters occurs inside a large transaction, and is deadlock prone.