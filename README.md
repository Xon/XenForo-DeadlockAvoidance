# XenForo-DeadlockAvoidance

Some hacks to avoid XenForo design issues which trigger DB errors

- Watch thread toggle
- Deadlock on conversations

### Deadlock on conversations

Provides a workaround for a XenForo design issue where updating conversation counters can cause deadlocks.