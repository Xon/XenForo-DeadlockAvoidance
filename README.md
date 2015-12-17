# XenForo-DeadlockAvoidance

Some hacks to avoid XenForo design issues which trigger DB errors

- Watch thread toggle
- Deadlock on conversations
- When alerts are updated due to adding/deleting content.

### Deadlock on conversations

Provides a workaround for a XenForo design issue where updating conversation counters can cause deadlocks.