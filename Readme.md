# Commission Fee Caculator

### How to start ?
You need to install depecdencies by running following command:
```bash
composer install
```
for running app you need to provide a csv file to the following command:
```bash
php bin/console app:calculate-commission input.csv 
```

then you should see the result there.
```cmd
Input file: input.csv
===========================
0.60
3.00
0.00
0.06
1.50
0
0.70
0.30
0.30
3.00
0.00
0.00
8612
```
For running tests you can start as following:
```bash
php bin/phpunit
```

There are configuration settings in `services.yaml` file which you can change following configuration:
```yaml
  apikey: '%env(LAYER_API_KEY)%'
  exchangefile: 'exrate.json' # exchange cache file
  base_currency: 'EUR' # base currency
  precision: 2 # precision for decimal places on final number formatting
  auto_update: false # Updates exchange file on each run
```

#### Requirements
- php 8.2+
- composer

---
### Description

This script allows `private` and `business` clients to `deposit` and `withdraw` funds to and from our accounts in multiple currencies. Clients may be charged a commission fee based on defined rules.

## Rules
- Commission fee is always calculated in the currency of the operation. For example, if you `withdraw` or `deposit` in US dollars then commission fee is also in US dollars.
Commission fees are rounded up to currency's decimal places. For example, `0.023 EUR` should be rounded up to `0.03 EUR`.
- #### Deposit Rules
  - All deposits are charged `0.03%` of deposit amount
- #### Withdraw rules
  - There are different calculation rules for withdraw of private and business clients:
    - ##### Private Clients
      - Commission fee - 0.3% from withdrawn amount.
      - 1000.00 EUR for a week (from Monday to Sunday) is free of charge. Only for the first 3 withdraw operations per a week. 4th and the following operations are calculated by using the rule above (0.3%). If total free of charge amount is exceeded them commission is calculated only for the exceeded amount (i.e. up to 1000.00 EUR no commission fee is applied).
      - For the second rule it will convert operation amount if it's not in Euros. It uses rates provided by https://api.exchangeratesapi.io/latest.
    - ##### Business Clients
        - Commission fee - 0.5% from withdrawn amount.

