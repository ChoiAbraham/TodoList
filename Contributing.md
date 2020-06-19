#Contributing Todo & Co

##Installation

####Fork Project
Click on "Fork" in https://github.com/ChoiAbraham/TodoList (top right)

This will take an entire duplicate instance of the project and live in your github account.

####Clone Project

Copy link of the project in your account `https://github.com/<your-github-account>/TodoList.git`

```
$ git clone https://github.com/<your-github-account>/TodoList.git
$ cd ToDo-Co
```
add Composer dependencies libraries
```
composer install
```
Set database parameters and install database (with windows)
```
php bin/console doctrine:database:create

php bin/console doctrine:schema:update --force
```

##Rules and Tests

- [Best Practices](https://symfony.com/doc/3.4/best_practices/index.html) of Symfony 3.4
- PSR1, PSR12 has to be followed. Rules are set in file : phpcs.xml.dist
- Add unit tests to prove that the bug is fixed or that the new feature actually works;
- Do atomic and logically separate commits (use the power of git rebase to have a clean and logical history);
- Never fix coding standards in some existing code as it makes the code review more difficult;
- Write good commit messages.

List errors with
```
phpcs
```
Fix errors with
```
phpcbf
```
Tests are to be valid before pull requests (on windows)
```
./vendor/bin/phpunit.bat
```


##How to Pull Request

Work on a Branch
```
git branch <name of the branch>
git checkout <name of the branch>
```

Add modifications, commit and Pull Request
```
git status
git add .
git commit -m 'your commit'
git push origin <name of the branch>
```
it will compare your modifications to the original repository TodoList

Someone will check your code and merge it eventually.

