-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2024 at 09:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `west`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`, `date_created`, `date_updated`) VALUES
(1, 'Concept Presentation', '2022-07-18 01:34:56', '2022-11-19 22:22:17'),
(2, 'Thesis Defense 20%', '2022-07-18 01:35:13', '2022-11-19 22:22:35'),
(3, 'Thesis Defense 50%', '2022-11-19 22:23:05', '2022-11-19 22:23:05'),
(4, 'Thesis Final Defense', '2022-11-19 22:23:05', '2022-11-19 22:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `chat_id` int(11) NOT NULL,
  `incoming_id` int(11) NOT NULL,
  `outgoing_id` int(11) NOT NULL,
  `sender_type` enum('student','instructor','adviser') NOT NULL,
  `message` text DEFAULT NULL,
  `message_type` enum('text','file','image') NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`chat_id`, `incoming_id`, `outgoing_id`, `sender_type`, `message`, `message_type`, `date_created`) VALUES
(1, 2, 9, 'student', 'test', 'text', '2022-11-19 14:27:17'),
(2, 9, 2, 'student', 'test', 'text', '2022-11-19 14:29:57'),
(3, 2, 9, 'student', '11192022-103020!I_I!prod-1.jpg', 'image', '2022-11-19 14:30:20'),
(4, 9, 2, 'student', 'test', 'text', '2022-11-20 03:19:03'),
(5, 3, 9, 'student', 'test', 'text', '2022-11-27 02:57:08'),
(6, 3, 9, 'student', '11272022-110411!I_I!SORTING PAHO.docx', 'file', '2022-11-27 03:04:11'),
(7, 3, 9, 'student', '11272022-110535!I_I!Screenshot 2022-11-19 211410.jpg', 'image', '2022-11-27 03:05:35');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `short_name` varchar(32) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `name`, `short_name`, `date_created`, `date_updated`) VALUES
(3, 'BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY', 'BSIT', '2022-11-20 04:54:22', '2022-11-20 04:59:57'),
(5, 'BACHELOR OF SCIENCE IN TEST', 'BST', '2022-11-20 04:54:22', '2022-11-20 04:59:57');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `leader_id` int(11) DEFAULT NULL,
  `title` text NOT NULL,
  `type_id` int(11) NOT NULL,
  `year` varchar(32) NOT NULL,
  `description` text NOT NULL,
  `img_banner` varchar(100) NOT NULL,
  `project_document` varchar(100) NOT NULL,
  `adviser_feedback` text DEFAULT NULL,
  `instructor_feedback` text DEFAULT NULL,
  `panel_rate_status` enum('APPROVED','DISAPPROVED') DEFAULT NULL,
  `concept_status` enum('APPROVED','DECLINED','PENDING') DEFAULT 'PENDING',
  `publish_status` enum('PENDING','TO PUBLISH','PUBLISHED') NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `leader_id`, `title`, `type_id`, `year`, `description`, `img_banner`, `project_document`, `adviser_feedback`, `instructor_feedback`, `panel_rate_status`, `concept_status`, `publish_status`, `date_created`, `date_updated`) VALUES
(1, 9, 'Sample title', 1, '2022', '<p>Sample description</p>', '/media/documents/banner/11192022-091507_Screenshot 2022-11-19 211410.jpg', '/media/documents/files/11192022-091507_pdfjs-express-demo.pdf', NULL, NULL, 'DISAPPROVED', 'DECLINED', 'PENDING', '2022-11-19 13:15:07', '2023-01-04 16:45:03'),
(4, 9, 'Sample title', 1, '2022', '<p>Sample description</p>', '/media/documents/banner/11192022-091507_Screenshot 2022-11-19 211410.jpg', '/media/documents/files/11192022-091507_pdfjs-express-demo.pdf', NULL, NULL, 'APPROVED', 'APPROVED', 'PENDING', '2022-11-19 13:15:07', '2023-01-04 16:45:04'),
(5, 9, 'Sample title', 1, '2022', '<p>Sample description</p>', '/media/documents/banner/11192022-091507_Screenshot 2022-11-19 211410.jpg', '/media/documents/files/11192022-091507_pdfjs-express-demo.pdf', NULL, NULL, 'DISAPPROVED', 'DECLINED', 'PENDING', '2022-11-19 13:15:07', '2023-01-04 16:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `instructor_sections`
--

CREATE TABLE `instructor_sections` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `sections` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor_sections`
--

INSERT INTO `instructor_sections` (`id`, `instructor_id`, `sections`) VALUES
(1, 2, '[{\"id\":\"5\",\"name\":\"BACHELOR OF SCIENCE IN TEST\",\"shortName\":\"BST\",\"sections\":[\"4-A\",\"4-B\"]},{\"id\":\"3\",\"name\":\"BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY\",\"shortName\":\"BSIT\",\"sections\":[\"4-C\",\"4-B\"]}]');

-- --------------------------------------------------------

--
-- Table structure for table `invite`
--

CREATE TABLE `invite` (
  `id` int(11) NOT NULL,
  `adviser_id` int(11) NOT NULL,
  `leader_id` int(11) NOT NULL,
  `status` enum('PENDING','APPROVED','DECLINED') NOT NULL,
  `proposed_title` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invite`
--

INSERT INTO `invite` (`id`, `adviser_id`, `leader_id`, `status`, `proposed_title`, `date_created`) VALUES
(1, 3, 9, 'APPROVED', 'test', '2022-11-27 02:55:52');

-- --------------------------------------------------------

--
-- Table structure for table `panel_ratings`
--

CREATE TABLE `panel_ratings` (
  `rating_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `leader_id` int(11) NOT NULL,
  `panel_id` int(11) NOT NULL,
  `rating_type` enum('concept','20percent','50percent','final') NOT NULL,
  `comment` text DEFAULT NULL,
  `action` enum('Approved','Disapproved') NOT NULL,
  `group_grade` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`group_grade`)),
  `individual_grade` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`individual_grade`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `panel_ratings`
--

INSERT INTO `panel_ratings` (`rating_id`, `document_id`, `leader_id`, `panel_id`, `rating_type`, `comment`, `action`, `group_grade`, `individual_grade`) VALUES
(13, 1, 9, 4, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(18, 4, 9, 4, 'concept', '<p>test</p>', 'Approved', 'null', 'null'),
(19, 5, 9, 4, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(20, 1, 9, 5, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(21, 4, 9, 5, 'concept', '<p>test</p>', 'Approved', 'null', 'null'),
(22, 5, 9, 5, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(23, 1, 9, 6, 'concept', '<p>test</p>', 'Approved', 'null', 'null'),
(24, 4, 9, 6, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(25, 5, 9, 6, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(27, 1, 9, 7, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(28, 4, 9, 7, 'concept', '<p>test</p>', 'Approved', 'null', 'null'),
(37, 5, 9, 7, 'concept', '<p>test</p>', 'Disapproved', 'null', 'null'),
(46, 4, 9, 4, 'final', '<p>test</p>', 'Approved', '{\"technical\":{\"title\":\"General Technical Criteria\",\"ratings\":[{\"title\":\"RELIABILITY\",\"description\":\"Extent to which a software can be expected to perform Its intended function with required precision (i.e. dependable. the probability of failure is low)\",\"name\":\"technical_a\",\"rating\":\"6\"},{\"title\":\"EFFICIENCY\",\"description\":\"Minimal amount of computing resources and code required by the software to perform its function\",\"name\":\"technical_b\",\"rating\":\"5\"},{\"title\":\"USABILITY\",\"description\":\"Effort required to learn and operate the in a user friendly manner (i.e. interface is consistent and stimulates user\'s, appropriate environment, easy to use)\",\"name\":\"technical_c\",\"rating\":\"5\"},{\"title\":\"UNDERSTANDABILITY\",\"description\":\"Degree to which source provides meaningful documentation, interactions of the sofWare components can be quickly understood, and the design is well\",\"name\":\"technical_d\",\"rating\":\"5\"},{\"title\":\"APPROPRIATENESS OF FEEDBACK TO USER\",\"description\":\"Instructions of error message and understandable and directions are clear as to what the user must do to use the software effectively\",\"name\":\"technical_e\",\"rating\":\"5\"},{\"title\":\"NAVIGATION AND ORGANIZATION\",\"description\":\"Users can progress Intuitively throughout the entre software in a logical path to find information. All buttons and navigational tools work\",\"name\":\"technical_f\",\"rating\":\"5\"}]},\"presentation\":{\"title\":\"General Presentation Criteria\",\"ratings\":[{\"title\":\"PREPARATION\",\"description\":\"Proponents hae adequately prepared for the presentation as indicated by smooth, comprehensive. concise and efficient delivery and quick and accurate responses to jurors\' questions\",\"name\":\"presentation_a\",\"rating\":\"5\"},{\"title\":\"SYNTHESIS\",\"description\":\"Proponents have a grasp of the objectives ot the thesis and SAD principles and methods\",\"name\":\"presentation_b\",\"rating\":\"5\"}]},\"multimedia\":{\"title\":\" Specific Technical Criteria for Multimedia Technologies (Educational, Interactive or Game)\",\"ratings\":[{\"title\":\"CONTENT AND DESIGN\",\"description\":\"<div>\\n                      <div>\\n                        <label>\\n                          <i>(For Educational\\/Interactive) <\\/i>\\n                        <\\/label>\\n                      <\\/div>\\n                      There is clear attention given to balance, proportion, harmony and restraint. The synergy reaches the intended audience with style.\\n                    <\\/div>\\n                    <div>\\n                      <div>\\n                        <label>\\n                          <i>(For Game)<\\/i>\\n                        <\\/label>\\n                      <\\/div>\\n                      The user easily the goal of the game, functionality (the way the game works) changes relative to adjustments made by the user, and it uses facts, statistics, reference materials or tools in the actual activity.\\n                    <\\/div>\",\"name\":\"multimedia_a\",\"rating\":\"5\"},{\"title\":\" USE OF ENHANCEMENT\",\"description\":\" Graphics, video, audio, or other enhancements are used effectively to enrich the experience. Enhancement contribute significantly to convey the intended\",\"name\":\"multimedia_b\",\"rating\":\"5\"}]},\"information\":{\"title\":\"Specific Technical Criteria for Information Systems & Prototype Software Systems\",\"ratings\":[{\"title\":\"CORRECTNESS\",\"description\":\"Extent to which a program satisfies Its specification and fulfill end-user\'s objective (I.e. specifications and software are equivalent)\",\"name\":\"information_a\",\"rating\":\"5\"},{\"title\":\"INTEGRITY\",\"description\":\"Extent to which access to software or data can be controlled by the security feature of the program.\",\"name\":\"information_b\",\"rating\":\"4\"}]}}', 'null');

-- --------------------------------------------------------

--
-- Table structure for table `schedule_list`
--

CREATE TABLE `schedule_list` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `leader_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `schedule_from` datetime NOT NULL,
  `schedule_to` datetime DEFAULT NULL,
  `is_whole` tinyint(4) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule_list`
--

INSERT INTO `schedule_list` (`id`, `user_id`, `category_id`, `leader_id`, `title`, `description`, `schedule_from`, `schedule_to`, `is_whole`, `date_created`, `date_updated`) VALUES
(6, 1, 1, 9, 'test', 'test', '2022-12-29 12:09:00', '2022-12-29 14:11:00', 0, '2022-12-29 12:09:51', '2022-12-29 12:09:51'),
(8, 1, 4, 9, 'test', 'test', '2023-01-05 09:07:00', '2023-01-05 10:07:00', 0, '2023-01-05 09:07:36', '2023-01-05 21:45:41');

-- --------------------------------------------------------

--
-- Table structure for table `system_config`
--

CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `system_name` text NOT NULL,
  `home_content` text NOT NULL,
  `cover` varchar(250) NOT NULL,
  `logo` varchar(250) NOT NULL,
  `contact` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_config`
--

INSERT INTO `system_config` (`id`, `system_name`, `home_content`, `cover`, `logo`, `contact`) VALUES
(1, 'Thesis Progress Monitoring and Archive Management System', '<p style=\"margin-right: 0px; margin-bottom: 1em; margin-left: 0px; font-size: inherit; text-align: inherit; -webkit-font-smoothing: antialiased; word-break: break-word; overflow-wrap: break-word; border: none; line-height: 1.476; padding: 0px;\">The College of Information and Communications Technology seeks to develop globally competent ICT professionals, sufficiently equip with appropriate knowledge, skills and attitude, for them to effectively design, develop, implement and manage information and communications technology resources in multi-disciplinary fields.</p><p style=\"margin-right: 0px; margin-bottom: 1em; margin-left: 0px; font-size: inherit; text-align: inherit; -webkit-font-smoothing: antialiased; word-break: break-word; overflow-wrap: break-word; border: none; line-height: 1.476; padding: 0px;\"><span style=\"-webkit-font-smoothing: antialiased; word-break: break-word; overflow-wrap: break-word; font-weight: 700;\">Specifically, the college endeavors to:</span></p><p style=\"margin-right: 0px; margin-bottom: 1em; margin-left: 0px; font-size: inherit; text-align: inherit; -webkit-font-smoothing: antialiased; word-break: break-word; overflow-wrap: break-word; border: none; line-height: 1.476; padding: 0px;\"><span style=\"-webkit-font-smoothing: antialiased; word-break: break-word; overflow-wrap: break-word; font-weight: 700;\"></span></p><ul style=\"margin-bottom: 0px; padding-inline-start: 48px;\"><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 11pt; font-family: Arial; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 14pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; text-wrap: wrap;\">Produce globally and quality graduates who have acquired knowledge and technical skills, have developed personal and social values adaptive to the work environment;</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 11pt; font-family: Arial; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 14pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; text-wrap: wrap;\">Inculcate in its students value of independent and life-long learning;</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 11pt; font-family: Arial; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 14pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; text-wrap: wrap;\">Provide valuable services to and share expertise and facilities and various stakeholders on the transfer and promotion </span><span style=\"background-color: transparent; font-size: 14pt; text-wrap: wrap;\">of information and communication technology for local, regional and national benefit;</span></p></li><li dir=\"ltr\" aria-level=\"1\" style=\"list-style-type: disc; font-size: 11pt; font-family: Arial; color: rgb(0, 0, 0); background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; white-space: pre;\"><p dir=\"ltr\" role=\"presentation\" style=\"margin-top: 0pt; margin-bottom: 0pt; line-height: 1.38;\"><span style=\"font-size: 14pt; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; vertical-align: baseline; text-wrap: wrap;\">Harness and undertake relevant ICT research directed towards mission-critical, public-service-sensitive, development- </span><span style=\"background-color: transparent; font-size: 14pt; text-wrap: wrap;\">management-supportive, and review-generating areas</span></p></li></ul>', '/public/cover-1638840281.jpg', '/public/10172022-112443_logo-1657357283.png', '09854698789 / 78945632');

-- --------------------------------------------------------

--
-- Table structure for table `thesis_groups`
--

CREATE TABLE `thesis_groups` (
  `id` int(11) NOT NULL,
  `group_leader_id` int(11) NOT NULL,
  `group_number` int(11) NOT NULL,
  `group_member_ids` varchar(50) NOT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `panel_ids` varchar(100) DEFAULT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thesis_groups`
--

INSERT INTO `thesis_groups` (`id`, `group_leader_id`, `group_number`, `group_member_ids`, `instructor_id`, `panel_ids`, `adviser_id`, `date_created`, `date_updated`) VALUES
(2, 9, 1, '[\"10\",\"11\",\"12\",\"13\"]', 2, '[\"4\",\"5\",\"6\",\"7\"]', 3, '2022-11-20 03:13:30', '2022-11-27 02:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `name`, `date_created`, `date_updated`) VALUES
(1, 'Data mining', '2022-10-21 13:17:20', '2022-10-25 01:50:39'),
(2, 'Robotics', '2022-10-21 13:17:20', '2022-10-25 01:50:39'),
(3, 'Test', '2022-10-21 13:17:20', '2022-10-25 01:50:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `roll` varchar(250) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `first_name` varchar(250) NOT NULL,
  `middle_name` varchar(250) DEFAULT NULL,
  `last_name` varchar(250) NOT NULL,
  `school_year` text DEFAULT NULL,
  `group_number` int(11) DEFAULT NULL,
  `year_and_section` varchar(32) DEFAULT NULL,
  `avatar` varchar(250) DEFAULT NULL,
  `username` varchar(500) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `role` enum('student','instructor','coordinator','panel','adviser') NOT NULL,
  `isLeader` tinyint(1) DEFAULT NULL,
  `leader_id` int(11) DEFAULT NULL,
  `is_new` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `roll`, `course_id`, `first_name`, `middle_name`, `last_name`, `school_year`, `group_number`, `year_and_section`, `avatar`, `username`, `email`, `password`, `role`, `isLeader`, `leader_id`, `is_new`, `date_added`, `date_updated`) VALUES
(1, NULL, NULL, 'coordinator', 'coordinator', 'coordinator', NULL, NULL, NULL, '/media/avatar/10162022-031509_10152022-111537_10072022-033907_avatar4.png', 'coordinator-coordinator-YZNlsAI7LOqw', 'coordinator@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$OXRvbjgxWUpnMW5mZU00cA$+aapaOG+CDk1+hgObV+ODcnlmTazsF7MpKS823s6+qY', 'coordinator', NULL, NULL, 0, '2022-09-28 03:58:39', '2022-11-19 12:23:27'),
(2, NULL, NULL, 'instructor', 'instructor', 'instructor', NULL, NULL, NULL, '/media/avatar/11212022-113445_Lencioco-ERD.png', 'instructor-instructor-K5xLfWDSAB2', 'instructor@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$L2FNYW5WVWRkdmdaUEdWMw$uPv0wtzUAg8Wx8wKIZJ5fLClydIjhl03fYzQGGE/3Dk', 'instructor', NULL, NULL, 0, '2022-11-19 20:45:03', '2022-11-21 03:34:45'),
(3, NULL, NULL, 'adviser', 'adviser', 'adviser', NULL, NULL, NULL, '/media/avatar/11192022-084828_avatar5.png', 'adviser-adviser-icTsV1HGgQW7', 'adviser@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$clBqU0kxdVRQYkN6N0pCQQ$kR6XnFaD4SQJago7F1JBnyR0daH3MYX4qgb80f4jkY0', 'adviser', NULL, NULL, 0, '2022-11-19 20:48:27', '2022-11-27 02:56:23'),
(4, NULL, NULL, 'panel', NULL, 'one', NULL, NULL, NULL, '/media/avatar/11192022-085150_user1-128x128.jpg', 'panel-one-iuhpigLFyXAi', 'panel_one@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$Z29lME5weGtzRndwVFIvUw$qXh99wqFL6XiczKcJG6wGP/LVwUsA1pfKRJnags/fv4', 'panel', NULL, NULL, 0, '2022-11-19 20:51:50', '2022-11-19 14:19:11'),
(5, NULL, NULL, 'panel', NULL, 'two', NULL, NULL, NULL, '/media/avatar/11192022-085253_user2-160x160.jpg', 'panel-two-z2P1xZQ8HUC', 'panel_two@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$TUtKdXdlOXFNUWE4QXZ1Zw$Yn4C9uS1C7dv40+yiCSxZGn+bkPilAge2P7+Lh2aZz4', 'panel', NULL, NULL, 0, '2022-11-19 20:52:53', '2022-11-20 03:46:19'),
(6, NULL, NULL, 'panel', NULL, 'three', NULL, NULL, NULL, '/media/avatar/11192022-085421_user3-128x128.jpg', 'panel-three-UMRcDuxaaFdK', 'panel_three@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$Z09mMG5RUWdqZDMuTllCTQ$gBc7eVHqJxSZKmYqQEM/HV8bAho0ge+hRMSkhjEGamE', 'panel', NULL, NULL, 0, '2022-11-19 20:54:21', '2023-01-04 13:44:55'),
(7, NULL, NULL, 'panel', NULL, 'four', NULL, NULL, NULL, '/media/avatar/11192022-085520_user4-128x128.jpg', 'panel-four-zhOwMXLTVM7i', 'panel_four@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$M3B5Q21rdnJNazhiMjlpbQ$78Q0MhWC0ks+3W0kcj/3xEBPfp6kTjwvW7pfFcMO+wE', 'panel', NULL, NULL, 0, '2022-11-19 20:55:19', '2023-01-04 16:22:07'),
(9, '2468', 3, 'leader', 'leader', 'leader', 'SY: 2021-23', 1, '4-A', NULL, 'leader-leader-CAE4ZGsOtp4D', 'leader@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$U1plbnVHUzFabWZaYkZzVQ$aGENFN5EPdwTipO8FOvqApI/GVkoIyWBC8Biaww/wHo', 'student', 1, NULL, 0, '2022-11-19 20:59:36', '2022-11-27 03:01:21'),
(10, '1357', 3, 'student', NULL, 'one', 'SY: 2021-22', 1, '4-A', NULL, 'student-one-9Z5ldidSW02B', 'student_one@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$aFh2ak42OWsxdzk3U09mNg$KzCFzg8okp0DbK+7tK6yF3IBB2OdWjDw74X2p8e2M/s', 'student', NULL, 9, 0, '2022-11-19 21:05:34', '2022-11-20 14:44:08'),
(11, '12345', 3, 'student', NULL, 'two', 'SY: 2021-22', 1, '4-A', NULL, 'student-two-VtiFj9Rr1aAk', 'student_two@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$YTVJUUVDNkREZTZLcURUeg$9xB67KpblUoVOgZ4r0fBgFkuYDHoaAnQdV22CG8ZxdY', 'student', NULL, 9, 0, '2022-11-19 21:06:16', '2022-11-20 14:44:08'),
(12, '54321', 3, 'student', NULL, 'three', 'SY: 2021-22', 1, '4-A', NULL, 'student-three-6UDvBHYuvp0f', 'student_three@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$d2ptNjlnQ0U5UHZWNHNleg$BbRp+NB/DAwpObC3Zyu2gLCLntFTKQyknWz0CuAWaRc', 'student', NULL, 9, 0, '2022-11-19 21:07:00', '2022-11-20 14:44:08'),
(13, '98765', 3, 'student', NULL, 'four', 'SY: 2021-22', 1, '4-A', NULL, 'student-four-u5X9QiWhR4eC', 'student_four@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$Zk1VR3Y0dzFwandIUTFvaw$vYXUDcoKoYvVjxN8jSaBPpyDyuH65u4CMHMv+WI0zC4', 'student', NULL, 9, 0, '2022-11-19 21:07:31', '2022-11-20 14:44:08'),
(14, NULL, NULL, 'Regin', 'A', 'Cabacas', NULL, NULL, NULL, NULL, 'regin-cabacas-SQfkb61TUkP8', 'adviser1@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$bVFRR2lIa0l2VUNqMGhjQQ$417FdzPFnNfhS55e+P9jFJ7OxuJemr6+78iY6FSZMN0', 'adviser', NULL, NULL, 1, '2022-11-21 22:10:20', '2022-11-21 14:10:21'),
(15, NULL, NULL, 'Ma. Beth ', 'S', 'Concepcion', NULL, NULL, NULL, NULL, 'ma. beth -concepcion-LSNTWZQnPTmh', 'adviser2@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$SDN1djd0cGFseTlEdEFHWg$3p7rDgUOSDbMJRgxAqzcGRiHOt1tI3hIuSGYp19mc5Q', 'adviser', NULL, NULL, 1, '2022-11-21 22:10:41', '2022-11-21 14:10:41'),
(16, NULL, NULL, 'Erwin ', NULL, 'Osorio', NULL, NULL, NULL, NULL, 'erwin -osorio-JBeG7YkHg95', 'adviser3@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$M015TXpUNE5xVVBhSUw2Zw$0bGUf1MoHSq2O7/kW4X7eKilsCS+JgzTZp+vpWFHli0', 'adviser', NULL, NULL, 1, '2022-11-21 22:11:07', '2022-11-21 14:11:08'),
(17, NULL, NULL, 'Nikie Jo', NULL, 'Deocampo', NULL, NULL, NULL, NULL, 'nikie jo-deocampo-Fo0k5CH8DQUq', 'adviser4@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$ZW90UUpuYTE0U2JjcEIwWg$OfHm8czwnBSVGumOI1HIDeQ3g+Pk6jWcbKO3fx6G0+g', 'adviser', NULL, NULL, 1, '2022-11-21 22:11:31', '2022-11-21 14:11:31'),
(18, NULL, NULL, 'Shem Durst Elijah ', NULL, 'Sandig', NULL, NULL, NULL, NULL, 'shem durst elijah -sandig-g3T81PldnAwx', 'adviser5@gmail.com', '$argon2i$v=19$m=65536,t=4,p=1$QTVrLjBScTRtTzNrU2JHLw$Clr6MZF5kSkUtg9XMjpEc5Ihp4h03yzuKZQXO5K8KrM', 'adviser', NULL, NULL, 1, '2022-11-21 22:11:55', '2022-11-21 14:11:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `instructor_sections`
--
ALTER TABLE `instructor_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invite`
--
ALTER TABLE `invite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panel_ratings`
--
ALTER TABLE `panel_ratings`
  ADD PRIMARY KEY (`rating_id`);

--
-- Indexes for table `schedule_list`
--
ALTER TABLE `schedule_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `thesis_groups`
--
ALTER TABLE `thesis_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `instructor_sections`
--
ALTER TABLE `instructor_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `invite`
--
ALTER TABLE `invite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `panel_ratings`
--
ALTER TABLE `panel_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `schedule_list`
--
ALTER TABLE `schedule_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `thesis_groups`
--
ALTER TABLE `thesis_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
