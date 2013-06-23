-- Make sure claims sent before the addition of the table pending_talk_claims are working with the new system
-- https://joindin.jira.com/browse/JOINDIN-271

Insert Into pending_talk_claims (talk_id, submitted_by, speaker_id, date_added, claim_id)
Select talk_id, null, speaker_id, null, id
From talk_speaker 
Where Status = 'pending';


Update talk_speaker 
Set speaker_id = null,
status = null
Where Status = 'pending';

INSERT INTO patch_history SET patch_number = 38;
