Find removed functions, classes and class methods between branches commits.

## Installation
1. Clone this repository someplace.
2. Install Composer dependencies: `composer update`.

## Usage
1. Checkout, using `git`, the first branch or commit you want to check, e.g. `git checkout main`.
2. Dump the functions, classes and methods defined in that branch or commit: `php dump.php /some-plugins/src main_`.
3. Checkout, using `git`, the second branch or commit you want to check, e.g. `git checkout some-branch`.
4. Dump the functions, classes and methods defined in that branch or commit: `php dump.php /some-plugins/src some_branch_`.
5. Use the `frf` script to list the functions removed between the first commit or branch and the second: `php frf.php main_ some_branch_`.

