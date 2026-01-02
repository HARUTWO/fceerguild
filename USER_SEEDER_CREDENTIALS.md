# User Seeder Credentials

## Students (10)
| Name | Email | Username | Password |
|------|-------|----------|----------|
| Juan Santos | jsantos@fceer.edu.ph | jsantos | jsantospassword |
| Maria Garcia | mgarcia@fceer.edu.ph | mgarcia | mgarciapassword |
| Carlos Reyes | creyes@fceer.edu.ph | creyes | creyespassword |
| Ana Diaz | adiaz@fceer.edu.ph | adiaz | adiazpassword |
| Pedro Cruz | pcruz@fceer.edu.ph | pcruz | pcruzpassword |
| Rosa Fernandez | rfernandez@fceer.edu.ph | rfernandez | rfernandezpassword |
| Miguel Torres | mtorres@fceer.edu.ph | mtorres | mtorrespassword |
| Isabel Lopez | ilopez@fceer.edu.ph | ilopez | ilopezpassword |
| Antonio Morales | amorales@fceer.edu.ph | amorales | amoralespassword |
| Luisa Rivera | lrivera@fceer.edu.ph | lrivera | lriverapassword |

## Administrators (5)
| Name | Email | Username | Password |
|------|-------|----------|----------|
| Admin One | adminone@fceer.edu.ph | adminone | adminonepassword |
| Admin Two | admintwo@fceer.edu.ph | admintwo | admintwopassword |
| Admin Three | adminthree@fceer.edu.ph | adminthree | adminthreepassword |
| Admin Four | adminfour@fceer.edu.ph | adminfour | adminfourpassword |
| Admin Five | adminfive@fceer.edu.ph | adminfive | adminfivepassword |

## Executives (5)
| Name | Email | Username | Password |
|------|-------|----------|----------|
| Executive One | execone@fceer.edu.ph | execone | execonepassword |
| Executive Two | exectwo@fceer.edu.ph | exectwo | exectwopassword |
| Executive Three | execthree@fceer.edu.ph | execthree | execthreepassword |
| Executive Four | execfour@fceer.edu.ph | execfour | execfourpassword |
| Executive Five | execfive@fceer.edu.ph | execfive | execfivepassword |

## System Managers (2)
| Name | Email | Username | Password |
|------|-------|----------|----------|
| System Manager One | sysmanagerone@fceer.edu.ph | sysmanagerone | sysmanageronepassword |
| System Manager Two | sysmanagertwo@fceer.edu.ph | sysmanagertwo | sysmanagertwopassword |

## Instructors (5)
| Name | Email | Username | Password |
|------|-------|----------|----------|
| Instructor One | instrone@fceer.edu.ph | instrone | instronepassword |
| Instructor Two | instrtwo@fceer.edu.ph | instrtwo | instrtwopassword |
| Instructor Three | instrthree@fceer.edu.ph | instrthree | instrthreepassword |
| Instructor Four | instrfour@fceer.edu.ph | instrfour | instrfourpassword |
| Instructor Five | instrfive@fceer.edu.ph | instrfive | instrfivepassword |

## Total: 27 Users
- 10 Students
- 5 Administrators
- 5 Executives
- 2 System Managers
- 5 Instructors

## Run seeder with:
```bash
php artisan db:seed
```

Or run individually:
```bash
php artisan db:seed --class=UserSeeder
```
