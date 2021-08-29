# Iji Utilities

医事的なユーティリティ(iji-utils)

請求点数から患者負担額を計算します。

## Installation

This library requires PHP 8.x.

```bash
$ git clone git@github.com:yokenzan/iji-utils-php.git
$ cd iji-utils-php
$ composer install
```

## Usage

### サブコマンド `calc:burden`

To show all options or arguments, run `calc:burden --help`.

```bash
# 請求点数30,000点
# (補完 : 患者定率負担割合3割)
# (補完 : 高額療養費所得区分ナシ)
$ bin/console calc:burden 30000
# => 90000
```

```bash
# 請求点数30,000点
# 患者定率負担割合3割
# 高額療養費所得区分ウ
# (補完 : 患者年齢区分70歳未満)
$ bin/console calc:burden \
  --patient-burden-rate=.3 \
  --kogaku-classification=u \
  30000
# => 80430
```

```bash
# 請求点数100,000点
# 患者年齢71歳(高齢受給者)
# 高額療養費所得区分現役並みⅢ
# 高額療養費多数回該当
# (補完 : 患者定率負担割合3割)
$ bin/console calc:burden 100000 \
  --patient-age=71 \
  --kogaku-classification=upper-3 \
  --kogaku-is-reduced
#=> 140100
```

## License

This library is available as open source under the terms of the [MIT License](https://opensource.org/licenses/MIT).

