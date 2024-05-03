# Covid-19-Contact-Tracking
This is a COVID-19 contact tracing web application. This project involves implementation of specific functionalities using HTML, CSS, PHP, and JavaScript.

The application comprises the following key components:

- [Login Page](#login-page)
- [Registration Page](#registration-page)
- [Home Page](#home-page)
- [Overview Page](#overview-page)
- [Add Visit Page](#add-visit-page)
- [Report Page](#report-page)
- [Settings Page](#settings-page)
- [Security Considerations](#security-considerations)

## Login Page 

Users access the application through a login screen, allowing registration and subsequent redirection to the home page upon successful authentication. Sessions are maintained to avoid repeated logins.

## Registration Page

The registration process includes mandatory inputs, password criteria validation, and encryption for stored passwords.

## Home Page

Upon login, users are directed to a personalized home page displaying their contact status and an overview of places visited by infected persons.

## Overview Page

This page presents a table showing the date, time, duration, and coordinates of each visited location, with an option to remove places from the user's list using JavaScript and AJAX.

## Add Visit Page

Users can input visit details, including date, time, duration, and coordinates by clicking on a map, triggering JavaScript functions.

## Report Page

Allows users to report infections, storing the information in the database and reporting visited locations to an external web service.

## Settings Page

Enables users to modify alert window and alert distance settings, stored in cookies.

## Security Considerations
Additionally, the project emphasises security considerations, including password encryption, protection against cross-site scripting attacks and SQL injections, and the use of secure sessions and cookies.