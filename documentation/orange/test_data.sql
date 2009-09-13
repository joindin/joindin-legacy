-- Users
INSERT INTO `users` (`id`, `username`, `password`, `email`, `display_name`, `active`, `admin`) VALUES
('1', 'mattijs', MD5('mattijs'), 'mattijshoitink@gmail.com', 'Mattijs Hoitink', 1, 1),
('2', 'enygma', MD5('enygma'), 'enygma@phpdeveloper.org', 'Chris Cornut', 1, 1),
('3', 'abe', MD5('abe'), 'abe@mail.abc', 'Abraham Sapien', 1, 0),
('4', 'batman', MD5('batman'), 'batman@mail.abc', 'Bruce Wayne', 1, 0),
('5', 'beast', MD5('beast'), 'beast@mail.abc', 'Henry Philip McCoy', 1, 0),
('6', 'bishop', MD5('bishop'), 'bishop@mail.abc', 'Lucas Bishop', 1, 0),
('7', 'blade', MD5('blade'), 'blade@mail.abc', 'Eric Brooks', 1, 0),
('8', 'captainamerica', MD5('captainamerica'), 'captainamerica@mail.abc', 'Steve Rogers', 1, 0),
('9', 'dannyphantom', MD5('dannyphantom'), 'dannyphantom@mail.abc', 'Daniel Fenton', 1, 0),
('10', 'daredevil', MD5('daredevil'), 'daredevil@mail.abc', 'Matt Murdock', 1, 0),
('11', 'flash', MD5('flash'), 'flash@mail.abc', 'Barry Allen', 1, 0),
('12', 'gambit', MD5('gambit'), 'gambit@mail.abc', 'Remy Etienne LeBeau', 1, 0),
('13', 'greenlantern', MD5('greenlantern'), 'greenlantern@mail.abc', 'Alan Scott', 1, 0),
('14', 'hellboy', MD5('hellboy'), 'hellboy@mail.abc', 'Anung Un Rama', 1, 0),
('15', 'he-man', MD5('he-man'), 'he-man@mail.abc', 'Prince Adam', 1, 0),
('16', 'hulk', MD5('hulk'), 'hulk@mail.abc', 'Bruce Banner', 1, 0),
('17', 'name', MD5('name'), 'email@mail.abc', 'Anthony Edward Stark', 1, 0),
('18', 'ironman', MD5('ironman'), 'ironman@mail.abc', 'Anthony Edward Stark', 1, 0),
('19', 'juggernaut', MD5('juggernaut'), 'juggernaut@mail.abc', 'Cain Marko', 1, 0),
('20', 'magneto', MD5('magneto'), 'magneto@mail.abc', 'Max Eisenhardt', 1, 0),
('21', 'mrfantastic', MD5('mrfantastic'), 'mrfantastic@mail.abc', 'Reed Richards', 1, 0),
('22', 'morph', MD5('morph'), 'morph@mail.abc', 'Kevin Sydney', 1, 0),
('23', 'punisher', MD5('punisher'), 'punisher@mail.abc', 'Frank Castle', 1, 0),
('24', 'quicksilver', MD5('quicksilver'), 'quicksilver@mail.abc', 'Pietro Django Maximoff', 1, 0),
('25', 'silversurfer', MD5('silversurfer'), 'silversurfer@mail.abc', 'Norrin Radd', 1, 0),
('26', 'spiderman', MD5('spiderman'), 'spiderman@mail.abc', 'Peter Parker', 1, 0),
('27', 'sunspot', MD5('sunspot'), 'sunspot@mail.abc', 'Roberto da Costa', 1, 0),
('28', 'superman', MD5('superman'), 'superman@mail.abc', 'Clark Joseph Kent', 1, 0),
('29', 'thing', MD5('thing'), 'thing@mail.abc', 'Benjamin Jacob Grimm', 1, 0),
('30', 'wolverine', MD5('wolverine'), 'wolverine@mail.abc', 'James Howlett', 1, 0),
('31', 'professorx', MD5('professorx'), 'professorx@mail.abc', 'Charles Francis Xavier', 1, 0),
('32', 'poisonivy', MD5('poisonivy'), 'poisonivy@mail.abc', 'Pamela Lillian Isley', 1, 0),
('33', 'blackcat', MD5('blackcat'), 'blackcat@mail.abc', 'Felicia Hardy', 1, 0),
('34', 'catwoman', MD5('catwoman'), 'catwoman@mail.abc', 'Selina Kyle', 1, 0),
('35', 'missamerica', MD5('missamerica'), 'missamerica@mail.abc', 'Joan Dale', 1, 0),
('36', 'twoface', MD5('twoface'), 'twoface@mail.abc', 'Harvey Dent', 1, 0),
('37', 'doctordoom', MD5('doctordoom'), 'doctordoom@mail.abc', 'Victor von Doom', 1, 0),
('38', 'puppetmaster', MD5('puppetmaster'), 'puppetmaster@mail.abc', 'Phillip Masters', 1, 0),
('39', 'lizard', MD5('lizard'), 'lizard@mail.abc', 'Curt Connors', 1, 0),
('40', 'galactus', MD5('galactus'), 'galactus@mail.abc', 'Galan', 1, 0),
('41', 'blackout', MD5('blackout'), 'blackout@mail.abc', 'Marcus Daniels', 1, 0),
('42', 'droctopus', MD5('droctopus'), 'droctopus@mail.abc', 'Otto Gunther Octavius', 1, 0),
('43', 'mysterio', MD5('mysterio'), 'mysterio@mail.abc', 'Quentin Beck', 1, 0)
;

-- Speaker profiles
INSERT INTO `speaker_profiles` (`id`, `user_id`, `full_name`, `contact_email`) VALUES
(1, 40, 'Galan', 'galactus@mail.abc'),
(2, 4, 'Bruce Wayne', 'batman@mail.abc'),
(3, 39, 'Dr. Curt Connors', 'lizard@mail.abc')
;

-- Talks
INSERT INTO `talks` (`id`, `speaker_profile_id`, `title`, `description`) VALUES
(1, 1, 'How to consume a world', "Just because I'm big does not mean it's easy to consume a world. In this talk I'll be going over the basics steps of world consumption."),
(2, 2, 'Racing Gothams streets safely', "Gotham is pretty big place, and a lot of people live their. It's not easy racing the streets catching criminals and keeping people safe at the same time. This talk will give you some tips on how to avoid getting the ones you prtect injured."),
(3, 3, 'Neogenises', 'Neogenises is the future, no doubt about that. This session will show some of the benefits of neogenises and what it can do for the human race. The talk will be closed with a status update on developments of my neogenises research.')
;
