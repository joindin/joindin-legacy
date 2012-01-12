<?php

class Talk_comments_model extends Model {

    function Talk_comments_model() {
        parent::Model();
    }
    //-------------------
    /**
     * Check to see if a talk with the given data already exists
     * @param array $data Talk data
     * @return boolean Is or Isn't Unique
     */
    public function isUnique($data) {
        $q=$this->db->get_where('talk_comments', $data);
        $ret=$q->result();
        return (empty($ret)) ? true : false;
    }
    
    /**
     * Check to see if the user has commented on a given talk
     * @param integer $talk_id Talk ID
     * @param integer $user_id User ID
     * @return boolean
     */
    public function hasUserCommented($talk_id, $user_id) {
        $data=array('talk_id'=>$talk_id,'user_id'=>$user_id);
        $q=$this->db->get_where('talk_comments', $data);
        $ret=$q->result();
        return (!empty($ret)) ? true : false;
    }
    
    /**
     * Get the comments for a specific user ID
     * @param integer $uid User ID
     * @return array Comment data
     */
    public function getUserComments($uid) {
        $this->db->from('talk_comments');
        $this->db->where('user_id', $uid);
        $q=$this->db->get();
        return $q->result();
    }
    
    /**
     * Remove a comment given its ID in the table
     * @param integer $cid Comment ID
     * @return null
     */
    public function deleteComment($cid) {
        $this->db->delete('talk_comments', array('id'=>$cid));
    }
    
    /**
     * Get the details on a comment given its ID
     * @param integer $cid Comment ID
     * @return array Comment data
     */
    public function getCommentDetail($cid) {
        $q=$this->db->get_where('talk_comments', array('ID'=>$cid));
        return $q->result();	
    }
    
    /**
     * Fetch comments for a given talk ID
     * @param integer $talk_id Talk ID
     * @return array Talk comment details
     */
    public function getEventComments($event_id) {
        $sql=sprintf('
            select
                t.ID as talk_id,
                tc.ID as comment_id,
                tc.rating
            from
                talks t,
                talk_comments tc
            where
                t.event_id=%s and
                t.ID=tc.talk_id;
        ', $this->db->escape($event_id));
        $q=$this->db->query($sql);
        return $q->result();
    }
}
?>
