# Final commercial release impact matrix

| File/module | Current status | Proposed final state | Legal impact | Packaging impact | Runtime impact | Required approval | Required test | Release-gate effect |
|---|---|---|---|---|---|---|---|---|
| `LICENSE` / policy guides | Draft absent; templates | Authoritative legal-review Single Site text and aligned summaries | Defines rights/restrictions; identity/law placeholders remain | Required in buyer packages | None | Owner/legal | Commercial audit consistency | BLOCKED until final header/approval |
| Composer/product metadata | No license field | `proprietary`; Buildora identity retained | Avoids open-source misclassification | Manifest metadata aligned | None | Owner confirms final classification | Composer validation | Supports license gate |
| Owner sign-off | Incomplete generic form | Explicit unsigned approvals | Records, never invents, legal decisions | Controls every final variant | None | Owner signature | Structured/manual review | BLOCKED |
| Acceptance JSON/Markdown | External gates blocked | Truthful BLOCKED evidence | Avoids false claims | Prevents approved packages | None | Owner/external reviewers | Commercial audit | BLOCKED |
| Commercial audit/config | Checks structured files | Validate legal status and all structured gate contents | Prevents draft from passing | Fails package approval closed | Audit only | Owner/legal for status change | Audit command tests | Strengthened |
| Installation documentation | Minimal flows | Source/shared host/VPS/local/web/CLI/clean/demo/activation/storage/cache/upgrade/recovery guidance | Avoids unsupported promises | Disables unapproved no-Composer claim | None | Vendor decision/version acceptance | Extracted ZIP installs | Accurate but blocked |
| External environments | Unavailable | Five disposable DBs, SMTP sandbox, real browser/accessibility, upgrade fixture | Evidence only | Required before ZIP approval | Test-only | Owner provisions | Phase 5–15 checks | BLOCKED |
| Assets/dependencies | Unverified/approval absent | Verified, excluded, or explicitly approved | Redistribution/provenance | Shared-hosting disabled | None | Owner/legal | Inventory/security scan | BLOCKED |
