# Changelog

## [5.1.0](https://github.com/dvsa/olcs-common/compare/v5.0.0...v5.1.0) (2024-02-16)


### Features

* merge `project/messaging` to main ([#41](https://github.com/dvsa/olcs-common/issues/41)) ([8f693f0](https://github.com/dvsa/olcs-common/commit/8f693f0d73e0674afded022cb502392123baccdd))

## [5.0.0](https://github.com/dvsa/olcs-common/compare/v5.0.0-beta.10...v5.0.0) (2024-02-14)


### ⚠ BREAKING CHANGES

* createService, getServiceLocator and ServiceLocatorInterface uses are now gone
* migrate to GitHub ([#2](https://github.com/dvsa/olcs-common/issues/2))

### Features

* drop support for Laminas v2 ([#3](https://github.com/dvsa/olcs-common/issues/3)) ([26c78bc](https://github.com/dvsa/olcs-common/commit/26c78bc9a1bac4f71933b573ef808a034f2c89f8))
* migrate to GitHub ([#2](https://github.com/dvsa/olcs-common/issues/2)) ([0a9748e](https://github.com/dvsa/olcs-common/commit/0a9748ed58e43b414dbe572383a0ff85bf98f3de))
* update Convictions and Penalties guidance ([#27](https://github.com/dvsa/olcs-common/issues/27)) ([91df066](https://github.com/dvsa/olcs-common/commit/91df0663623a37079bded6899509def553293f2e))
* VOL-4336 - Table formatter for conversation list ([f7cdb00](https://github.com/dvsa/olcs-common/commit/f7cdb0083b8b3d272a9a02495384cdbb38e6a702))
* VOL-4576 List Messages in Conversation ([#6](https://github.com/dvsa/olcs-common/issues/6)) ([7e8cff2](https://github.com/dvsa/olcs-common/commit/7e8cff216df08d1f9957c4c37e46419473271f09))


### Bug Fixes

* add `create_empty_option` to date fields that can be empty ([#24](https://github.com/dvsa/olcs-common/issues/24)) ([d60833e](https://github.com/dvsa/olcs-common/commit/d60833ee4af99da77c4ac52d7bf2bb04176a3630))
* add `priority` to correctly order TM details form ([#32](https://github.com/dvsa/olcs-common/issues/32)) ([fbdbe8c](https://github.com/dvsa/olcs-common/commit/fbdbe8cee4b41d62cb37c3cfa02762d9510ac7a0))
* add priority to forms to order elements/fieldsets better ([#25](https://github.com/dvsa/olcs-common/issues/25)) ([3610268](https://github.com/dvsa/olcs-common/commit/3610268de9fe738d29007a78542d39089618d22b))
* consolidate `Navigation` and `navigation` ([#31](https://github.com/dvsa/olcs-common/issues/31)) ([cd3b15c](https://github.com/dvsa/olcs-common/commit/cd3b15cff444d0625fe31e691680bb6f60c516c0))
* fix `GenerateContinuationDetails` `service_name` ([#19](https://github.com/dvsa/olcs-common/issues/19)) ([f39ce17](https://github.com/dvsa/olcs-common/commit/f39ce176aca8e544e72fc5b89fe1618e9865e231))
* fix casing of `ViewHelperManager` in call to service manager ([#26](https://github.com/dvsa/olcs-common/issues/26)) ([f8d584e](https://github.com/dvsa/olcs-common/commit/f8d584e73ca7ead2a1cc767ffeba241e9fc8d65a))
* fix textarea character count label translation issue ([#23](https://github.com/dvsa/olcs-common/issues/23)) ([f546db1](https://github.com/dvsa/olcs-common/commit/f546db16a97e41a630988aa559f25250a4a66da1))
* fix type error in `AbstractInputSearch` when `this-&gt;messages` is empty ([#20](https://github.com/dvsa/olcs-common/issues/20)) ([27558a0](https://github.com/dvsa/olcs-common/commit/27558a0d2b7cf49bf5a6061f15a1ac4662a8fa31))
* fix undefined `formHelper` variable ([#37](https://github.com/dvsa/olcs-common/issues/37)) ([530e47d](https://github.com/dvsa/olcs-common/commit/530e47d0d8bc3574ded6ed980694c866894979ba))
* keep `SearchPostcode` fieldset at the top of the `Address` fieldset ([#22](https://github.com/dvsa/olcs-common/issues/22)) ([74fcfd8](https://github.com/dvsa/olcs-common/commit/74fcfd813e1a24e262e466b6ae8b75a331bf733d))
* lowercasing formdatetimeselect. Custom helper wasnt being used and some func. was missing. ([#34](https://github.com/dvsa/olcs-common/issues/34)) ([732adc9](https://github.com/dvsa/olcs-common/commit/732adc92875e5acc7174e62dbde47d30ecf07af3))
* remove cache of element in `FileUploadHelperService` ([#29](https://github.com/dvsa/olcs-common/issues/29)) ([7c4648f](https://github.com/dvsa/olcs-common/commit/7c4648f7ac076445a93e5c2f5772566b2a20442b))
* remove form unit tests ([#9](https://github.com/dvsa/olcs-common/issues/9)) ([6cab5bd](https://github.com/dvsa/olcs-common/commit/6cab5bde0d23951d7c5bd2fe1d1d8c925fa8aa51))
* rename `LicenceChecklist` to `licenceChecklist` ([#30](https://github.com/dvsa/olcs-common/issues/30)) ([25584d8](https://github.com/dvsa/olcs-common/commit/25584d84ca03ceb86d9e992d956ed86bbb25b2bf))
* return empty strings in situations where getValue now returns null since L3 changes ([#33](https://github.com/dvsa/olcs-common/issues/33)) ([8a85a42](https://github.com/dvsa/olcs-common/commit/8a85a4229ec5465b8d99dd0eaee24c16fbbf1fd1))
* update `MAX_LENGTH` to `string` to fix type error ([#35](https://github.com/dvsa/olcs-common/issues/35)) ([1e75cd0](https://github.com/dvsa/olcs-common/commit/1e75cd04abd68c75950d90f49a40cfaa78ff6691))
* VOL-4847 get search working with Laminas 3 ([#18](https://github.com/dvsa/olcs-common/issues/18)) ([cce5be1](https://github.com/dvsa/olcs-common/commit/cce5be127209151bb076b5bb0c48b0f74a2da189))


### Miscellaneous Chores

* add Dependabot config ([#8](https://github.com/dvsa/olcs-common/issues/8)) ([c5737f8](https://github.com/dvsa/olcs-common/commit/c5737f8afb76843ae1bf8895c68fc60a1f98723d))
* release 5.0.0 ([#39](https://github.com/dvsa/olcs-common/issues/39)) ([0858a62](https://github.com/dvsa/olcs-common/commit/0858a62ea43e1bc04742cfb94d0d05219633b4b7))