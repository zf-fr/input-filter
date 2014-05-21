# Input Filter

This component is a prototype for ZF3 Input Filter. Its main advantages over original implementation are:

* Much more efficient (benchmarks showed a x10 performance and a lot less consumed memory).
* Completely stateless
* Uses a concept of "InputFilterResult"
* Renames to make it easier to understand (previous "InputFilter" has been renamed to "InputCollection")
* Behaviour is now much more predictable
* ... and a lot of things!

## How to use it?
