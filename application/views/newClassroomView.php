<div class="container pt-2">
<h1 class="text-center">Add New Classroom</h1>
        <form id="myForm" action="<?php echo site_url('insertClassroom'); ?>" method="POST">
            <div class="form-group">
                <label for="class_subject">Class Subject:</label>
                <select id="class_subject" class="form-control">
                    <option value="">Select a subject</option>
                </select>
            </div>
			<div class="form-group">
				<label for="class_StartTime">Class Begin Time:</label>
				<input type="time" id="class_StartTime" name="class_StartTime" required>
				<label for="class_EndTime" style="margin-left:20px">Class End Time:</label>
				<input type="time" id="class_EndTime" name="class_EndTime" required>
				<span id="time-error" style="color: red; display: none;">End time must be greater than start time and within 4 hours.</span>			
			</div>
            <div class="form-group">
                <label for="teacher_id">Select Teacher:</label>
                <select class="form-control" id="teacher_id" name="teacher_id" required>
                <?php foreach ($teacherCL as $teachers): ?>
                        <option value="<?php echo $teachers['Teacher_ID']; ?>">
                            <?php echo $teachers['Teacher_Name']; ?> (Current handling class: <?php echo $teachers['NumberOfClasses']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Add Students Into Classroom:</label>
                <div class="checkbox-container">
                <div class="form-group">
                    <input type="text" id="search-input" class="form-control" placeholder="Search by students name...">
                </div>
                    <div class="checkbox-list">
                    <div class="checkbox-item">
                        <label style="font-weight:normal;">
                            <input type="checkbox" id="select-all"> Select All
                        </label>
                    </div>
                        <?php foreach ($studentCL as $student): ?>
                            <div class="checkbox-item">
                            <label style="font-weight:normal;">
                                <input type="checkbox" name="students[]" value="<?php echo $student['Student_ID']; ?>"> 
                                <?php echo $student['Student_Name']; ?> (Current attending class: <?php echo $student['NumberOfCourses']; ?>)
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Classroom</button>
        </form>
</div>
<script>


fetch ('http://localhost/CAMS/class_subject.json')
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('class_subject')
        data.subjects.forEach(subject => {
            const option = document.createElement('option')
            option.value = subject;
            option.textContent = subject;
            select.appendChild(option);
        })
    })
    .catch (error => console.error("Error fetching class subjects:", error));

document.addEventListener('DOMContentLoaded', function() {
    var selectAllCheckbox = document.getElementById('select-all');
    var studentCheckboxes = document.querySelectorAll('input[name="students[]"]');
    const startTimeInput = document.getElementById('class_StartTime');
    const endTimeInput = document.getElementById('class_EndTime');
    const errorSpan = document.getElementById('time-error');

    selectAllCheckbox.addEventListener('change', function() {
        studentCheckboxes.forEach(function(checkbox) {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

	function validateTimes() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        if (startTime && endTime) {
            const start = new Date(`1970-01-01T${startTime}`);
            const end = new Date(`1970-01-01T${endTime}`);
            const diffInMinutes = (end - start) / (1000 * 60);
            if (end <= start || diffInMinutes > 240) {
                errorSpan.style.display = 'inline';
				endTimeInput.value = '';
                return false;
            } else {
                errorSpan.style.display = 'none';
                return true;
            }
        }
        return true;
    }
    startTimeInput.addEventListener('change', validateTimes);
    endTimeInput.addEventListener('change', validateTimes);
});
document.getElementById('myForm').addEventListener('submit', function(event) {
    var subjectField = document.getElementById('class_subject');
    var subject = subjectField.value.trim();
    if (subject.value === "") {
        alert("Please select a class subject.");
        subjectField.focus();
        event.preventDefault();
        return;
    }

    var checkboxes = document.querySelectorAll('input[name="students[]"]');
    var checked = Array.prototype.slice.call(checkboxes).some(function(checkbox) {
        return checkbox.checked;
    });
    if (!checked) {
        alert('Please add at least one student to the classroom.');
        event.preventDefault();
        return;
    }
});

document.getElementById('search-input').addEventListener('input', function() {
    var searchValue = this.value.toLowerCase();
    var checkboxes = document.querySelectorAll('.checkbox-item');

    checkboxes.forEach(function(item) {
        var label = item.querySelector('label').textContent.toLowerCase();
        if (label.includes(searchValue)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});

</script>
