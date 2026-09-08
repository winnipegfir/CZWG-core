# Winnipeg FIR Core 
### The website for the Virtual Winnipeg FIR on VATSIM - https://winnipegfir.ca

### Thank you to the team at the Gander Oceanic OCA for developing the initial core! [Click here to visit their repository.](https://github.com/gander-oceanic-fir-vatsim/czqo-core)
---
### Contributing

We would love to have your ideas put into the website. Anything can be put into either a pull request or an issue for our review!

#### Submitting an Issue or Pull Request
Guidelines for submitting an **issue**:

- Be sensible, and don't spam with unnecessary issues.
- Tell us:
  - What is the issue/feature?
  - Why does it need to be fixed/why is it important to add?
  - How can we reproduce the issue? (if it is a bug)
  - What have you already tried? (if it is a bug)

Guidelines for submitting a **pull request**:
- Be sensible as stated above.
- Tell us:
  - What you have fixed/added and where you fixed it
  - Why it was a problem, or why it was neccessary/nice to add
- Document/comment your code. This is important for us and future developers so they can understand what you have written.

### Initial setup process

1. Clone the repository.
1. Run `composer install`
1. Rename `.env.example` to `.env` and fill required fields. The VATSIM connect demo URI is already placed in there. Get your ID and put your redirect URI into http://auth-dev.vatsim.net.
1. Run `php artisan migrate --seed` (runs database migrations and seeds with required rows).
1. Run `php artisan key:generate`.
1. Login with one of the accounts found at http://wiki.vatsim.net/connect.
1. Give that new account in the `users` table a `permissions` value of `5`.


