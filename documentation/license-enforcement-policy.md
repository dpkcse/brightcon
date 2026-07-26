# License enforcement policy

Buildora CMS uses centralized named actions. Public viewing, ordinary administration, backup, export, license remediation and system recovery are always allowed. A valid entitlement is initially required only for `updates.download` and future explicitly named premium activation actions; no updater is included.

The levels are informational, updates restricted, premium restricted and administrative compliance warning. Release defaults are non-destructive: invalid licensing never deletes data, reopens installation, disables public pages, or prevents export. License state, installation state and demo seed state are independent.

Provider outages are not confirmed invalidity. A future provider declaring recurring remote validation may retain a prior valid result for the configured grace period; after grace, only updates/premium entitlement is restricted. The offline provider has no network checks and no artificial network grace.

This mechanism is commercial compliance and entitlement infrastructure, not an anti-piracy guarantee. Source redistribution remains governed by the final owner-approved commercial `LICENSE`, which is still a release blocker.
