drop database if exists PansAtHome;
create database PansAtHome;

use PansAtHome;

create table if not exists user (
	username varchar(30) primary key,
	email varchar(255),
	password char(60),
	pfp blob(25000000),
	points int,
	joinDate datetime,
	admin bool,
	banned bool,
	banReason varchar(2047)
);

create table if not exists post (
	postId int auto_increment primary key,
	title varchar(255),
	postImage blob(25000000),
	text varchar(4095),
	numComments int,
	upvotes int,
	downvotes int,
	username varchar(30),
	postDate datetime,
	foreign key (username) references user(username) on delete set null on update cascade
);

create table if not exists comment (
	commentId int auto_increment primary key,
	text varchar(4095),
	username varchar(30),
	upvotes int,
	downvotes int,
	postId int,
	commentDate datetime,
	foreign key (username) references user(username) on delete set null on update cascade,
	foreign key (postId) references post(postId) on delete cascade on update cascade
);

create table if not exists UserVotedComment (
	commentId int,
	username varchar(30),
	upvoted bool,
	downvoted bool,
	primary key (commentId, username),
	foreign key (commentId) references comment(commentId) on delete cascade on update cascade,
	foreign key (username) references user(username) on delete cascade on update cascade
);

create table if not exists UserVotedPost (
	postId int,
	username varchar(30),
	upvoted bool,
	downvoted bool,
	primary key (postId, username),
	foreign key (postId) references post(postId) on delete cascade on update cascade,
	foreign key (username) references user(username) on delete cascade on update cascade
);

create table if not exists Report (
	reportId int auto_increment primary key,
	reportedPost int,
	reportedComment int,
	reportingUser varchar(30),
	type varchar(8),
	reason varchar(255),
	foreign key (reportedPost) references post(postId) on delete cascade on update cascade,
	foreign key (reportedComment) references comment(commentId) on delete cascade on update cascade,
	foreign key (reportingUser) references user(username) on delete cascade on update cascade
);

create table if not exists BannedWords (
	word varchar(64)
);
