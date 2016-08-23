# Bank Account Example

Event Sourcing in TYPO3 CMS

This extension requires https://github.com/TYPO3Incubator/data_handling as base
integration framework for TYPO3 CMS.

## Disclaimer

This is experimental and for educational purposes. Consider what you're doing
if you aim to used this proof-of-concept example in production environments. 

## Projection

### Configuration

Projections are configured using `ProjectionPool`. This way, projections can
be assigned to dynamic stream names, event categories or (inheritable) event
class names.

See [Common.php](https://github.com/TYPO3Incubator/bank_account_example/blob/master/Classes/Common.php)
for further information.

### Adhoc Projection

Model data is projection during runtime on a per-event basis. Thus, events that
happened are immediately projected into the regular Extbase repositories.

### Stream Projection

However, in case the repository data on the read side got lost or was removed
on purpose, the data can be rebuild by triggering the stream projection.

Triggering stream projection in command line:

```
[vendor-dir]/typo3/sysext/core/bin/typo3 datahandler:build '$H4ck3r31-BankAccountExample/Bank'
```

This instructs the projection builder to start with stream `$H4ck3r31-BankAccountExample/Bank`
and to process all relations that are available as events in that stream. The `$stream` thingy
is an EventSelector that allows stream wildcards, category names and event names to be selected.
