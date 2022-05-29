<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://www.forseti.com.br/wp-content/uploads/2022/05/Logo-Forseti-Soluções-Todo-Branco.png" width="400"></a></p>

<h1 align="center">Forseti Code Challenge</h1>
<h2 align="center">Challenger: <a href="https://github.com/IsraelPinheiro">Israel Pinheiro</a></h1>

## :desktop_computer: Technologies Used

### Main Technologies

- PHP 8.1: As predetermined, PHP was used as the language for the challenge;
- Laravel 9.14: Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects;
- MySQL 8.0: MySQL was chosen for being one of the most used and well known database engines in the market;

### Other Technologies
- Composer: Application-level package manager for the PHP programming language that provides a standard format for managing PHP software dependencies and required libraries;


## :gear:Instalation, preparation and usage

- Step 1: Preparation

Have PHP, MySQL and Composer installed in versions compatible for the project

- Step 2: Cloning

```bash
$git clone git@github.com:IsraelPinheiro/forseti-code-challenge.git
```

- Step 3: Instalation

Access the folder of the cloned repo and run the following commands

```bash
$composer install
```

- Step 4: Environment Variables

Add/change the pertinent data on .env to reflect the local environment

```bash
DATABASE_HOST = {database_host}
DATABASE_NAME = {database_name}
DATABASE_PORT = {database_port}
DATABASE_USER = {database_user}
DATABASE_PASSWORD = {database_password}
```

Some basic conficurations for the web crawler can be made by editing the following keys on the .env file:

```bash
ITEMS_PER_PAGE = 30
MAX_PAGES_TO_CRAWL = 5
```

- Step 5: Database strucutre and default data

After configuring the DBMS access, the database structure must be provided, it can be done by running the following command:

```bash
$php artisan migrate --seed
```

- Step 6: Run! :runner:

Now, the application should be ready to run.
Access the root of your web server pointing to the cloned folder, use the internal PHP Web Server or the web server provided by Laravel Artisan using the following command

```bash
$php artisan serve
```

- Step 7: Automation

When using Laravel's scheduler, we only need to add a single cron configuration entry to our server that runs the schedule:run command every minute. 

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Typically, you would not add a scheduler cron entry to your local development machine. Instead, you may use the schedule:work Artisan command. This command will run in the foreground and invoke the scheduler every minute until you terminate the command:

```bash
php artisan schedule:work
```

- Step 8: Accessing Information

A basic API is provided so the information provided by the automated crawler can be accessed, manipulated and export. The API documentation can be found <a href='https://documenter.getpostman.com/view/3768689/Uz5CLxVx'>here</a>

## :envelope:Contact and Acknowledgments 

Have any question ?
You can contact me by the following channels:

<h3 style="text-align:left">Get in touch:</h3>

* <a href="https://api.whatsapp.com/send?phone=5585991520250">Whatsapp</a>
* <a href="https://t.me/israelrpinheiro">Telegram</a>
* <a href="mailto:israel.pinheiro@live.com">Email</a>
* <a href="https://www.linkedin.com/in/israelpinheiro">Linkedin</a>