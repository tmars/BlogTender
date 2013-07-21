DROP TRIGGER IF EXISTS content_like_counter;
DELIMITER $$
CREATE TRIGGER content_like_counter
AFTER INSERT ON content_object__like
FOR EACH ROW
BEGIN
	UPDATE content_object__object SET likes_count = likes_count + 1
	WHERE id=NEW.content_object_id;
END$$
DELIMITER ;