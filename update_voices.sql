SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE `songs`;
TRUNCATE TABLE `voices`;
SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `voices` (`name`, `file_path`) VALUES
('Instrumental - General', 'assets/placeholder.mp3'),
('Manele - Voce Masculina 1 (simulare)', 'assets/placeholder.mp3'),
('Manele - Voce Masculina 2 (simulare)', 'assets/placeholder.mp3'),
('Manele - Voce Feminina 1 (simulare)', 'assets/placeholder.mp3'),
('Manele (Instrumental)', 'assets/placeholder.mp3'),
('Dance - Voce Feminina 1 (simulare)', 'assets/placeholder.mp3'),
('Dance - Voce Masculina 1 (simulare)', 'assets/placeholder.mp3'),
('Dance (Instrumental)', 'assets/placeholder.mp3'),
('Pop - Voce Feminina 1 (simulare)', 'assets/placeholder.mp3'),
('Pop - Voce Masculina 1 (simulare)', 'assets/placeholder.mp3'),
('Pop (Instrumental)', 'assets/placeholder.mp3'),
('Hip Hop - Voce Masculina 1 (simulare)', 'assets/placeholder.mp3'),
('Hip Hop (Instrumental)', 'assets/placeholder.mp3'),
('Muzica Populara - Voce Feminina 1 (simulare)', 'assets/placeholder.mp3'),
('Muzica Populara - Voce Masculina 1 (simulare)', 'assets/placeholder.mp3'),
('Muzica Populara (Instrumental)', 'assets/placeholder.mp3'),
('Manele Remix (Instrumental)', 'assets/placeholder.mp3');
