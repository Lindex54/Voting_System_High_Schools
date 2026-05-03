HIGH SCHOOL VOTING SYSTEM
================================

A simple, reliable, and modern web-based voting system built for school elections.


PROJECT OVERVIEW
----------------

The Rock High School Voting System is a digital election management platform designed to help schools conduct student leadership elections in a faster, safer, and more organized way.

Instead of using paper ballots, manual counting, and long verification processes, this system allows administrators to create election positions, register voters, add candidates, open or close voting, and view results from one central dashboard. Students can securely log in, view available candidates, and submit their votes through a clean web interface.

The system was developed to make school elections more transparent, efficient, and easier to manage, especially in institutions where accuracy, fairness, and time matter.


WHY THIS SYSTEM WAS DEVELOPED
-----------------------------

School elections are important because they teach students leadership, responsibility, accountability, and democratic participation. However, traditional school elections can be difficult to manage.

Common challenges include:

- Paper ballots can be misplaced, damaged, or counted incorrectly.
- Manual vote counting takes time and can delay results.
- Duplicate voting may happen if voter records are not managed carefully.
- Administrators may struggle to track voter turnout by class or stream.
- Election reports are often prepared manually, which increases the chance of mistakes.

This voting system was created to solve those problems by providing a digital platform where election activities can be handled in a structured and dependable way.


IMPORTANCE OF THE SYSTEM
------------------------

This system is useful because it brings order, speed, and trust to the election process.

Its importance includes:

- Accuracy: Votes are recorded and counted by the system, reducing human counting errors.
- Transparency: Administrators can monitor election progress and view clear results.
- Security: Voters log in using assigned credentials, helping prevent unauthorized voting.
- Efficiency: Results can be generated quickly after voting closes.
- Organization: Voters, candidates, positions, and ballots are managed from one place.
- Accountability: Election reports can be printed and kept for school records.
- Student empowerment: Students experience a fair and modern election process.


WHO CAN USE IT
--------------

This system is ideal for:

- High schools
- Student councils
- Prefect body elections
- Class representative elections
- Club leadership elections
- Any small institution that needs a local voting platform


MAIN FEATURES
-------------

1. Admin Dashboard
   The administrator gets a central dashboard for managing the election and viewing key election information.

2. Voter Management
   Administrators can add, edit, import, and delete student voters. Each voter can be assigned details such as name, class, stream, and voting credentials.

3. Candidate Management
   Candidates can be added under specific positions. Their information and photos can also be managed from the admin area.

4. Position Management
   Election positions such as Head Prefect, Assistant Head Prefect, Class Representative, or Club President can be created and arranged.

5. Ballot Setup
   The system automatically organizes candidates under their respective positions so voters can make clear selections.

6. Election Status Control
   Administrators can open or close the election. This helps prevent votes from being submitted before or after the allowed voting period.

7. Secure Student Login
   Registered voters can log in and vote using their assigned credentials.

8. Vote Submission
   Each voter can submit their choices through the web interface. After voting, the system records the vote for counting.

9. Results Dashboard
   Administrators can view vote counts and monitor election results.

10. Printable Reports
    Election results can be printed for official school records.

11. Turnout Reporting
    The system supports class and stream turnout tracking, helping the school understand participation levels.


HOW THE SYSTEM WORKS
--------------------

Admin workflow:

1. Log in to the admin panel.
2. Add election positions.
3. Register or import voters.
4. Add candidates and assign them to positions.
5. Review the ballot preview.
6. Open the election when voting should begin.
7. Monitor submitted votes and turnout.
8. Close the election when voting ends.
9. View and print final results.

Student workflow:

1. Open the voter login page.
2. Enter the assigned voter credentials.
3. Review the ballot.
4. Select preferred candidates.
5. Submit the ballot.
6. Log out after voting.


SYSTEM REQUIREMENTS
-------------------

To run this project locally, you need:

- XAMPP
- Apache server
- MySQL or MariaDB
- PHP
- A modern web browser such as Chrome, Edge, or Firefox


INSTALLATION AND SETUP
----------------------

1. Install XAMPP on your computer.
2. Copy the project folder into the XAMPP htdocs directory.
   Example:
   C:\xampp\htdocs\votesystem

3. Start Apache and MySQL from the XAMPP Control Panel.

4. Open phpMyAdmin in your browser:
   http://localhost/phpmyadmin

5. Create a database named:
   votesystem

6. Import the project database file if you are starting from a fresh copy.

7. Open the system in your browser:
   http://localhost/votesystem

8. Open the admin panel:
   http://localhost/votesystem/admin/


DEFAULT LOCAL DATABASE CONNECTION
---------------------------------

Host: localhost
User: root
Password: empty
Database: votesystem

The database connection is configured in:

- includes/conn.php
- admin/includes/conn.php


ADMIN ACCESS
------------

Admin login page:
http://localhost/votesystem/admin/

Use the admin account created in the database to access the dashboard.


PROJECT STRUCTURE
-----------------

admin/
Contains the administrator dashboard and management pages for voters, candidates, positions, ballots, votes, reports, and election settings.

includes/
Contains shared PHP files such as database connection, sessions, layout components, and helper functions.

images/
Stores uploaded candidate, voter, and profile images.

dist/, plugins/, bower_components/
Contain frontend assets, styles, scripts, and interface libraries used by the system.

tcpdf/
Contains PDF/report generation tools used for printable election reports.


BENEFITS TO THE SCHOOL
----------------------

For administrators:
- Saves time during election preparation and vote counting.
- Reduces paperwork.
- Makes records easier to manage.
- Improves confidence in election results.

For students:
- Provides a simple and clear voting experience.
- Encourages participation.
- Builds trust in the election process.
- Introduces students to responsible digital citizenship.

For the institution:
- Creates a more professional election process.
- Supports transparent leadership selection.
- Keeps organized records for future reference.


SECURITY NOTES
--------------

This system is designed for local school election management. For best use:

- Keep admin credentials private.
- Change default passwords before a real election.
- Back up the database before opening voting.
- Only allow trusted staff to access the admin panel.
- Close the election immediately after voting ends.
- Keep the XAMPP server on a trusted school computer or network.


FUTURE IMPROVEMENTS
-------------------

Possible future upgrades include:

- Stronger role-based admin access
- Email or SMS voter verification
- Automatic election scheduling
- Student ID card scanning
- Detailed audit logs
- Mobile-first voting interface improvements
- Cloud deployment for wider access
- Export results to Excel or PDF summaries


CONCLUSION
----------

The Rock High School Voting System is more than a software project. It is a practical tool for improving fairness, speed, and confidence in school elections.

By replacing slow manual processes with a structured digital system, the school can run elections that are easier to manage, easier to understand, and more trusted by students and staff.

It supports a culture of leadership, responsibility, and democratic participation while giving administrators the tools they need to manage elections professionally.
