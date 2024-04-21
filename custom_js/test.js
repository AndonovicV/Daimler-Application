// Example array of members fetched from the database
var moduleTeamMembers = [
    'Doe, John',
    'Smith, Jane',
    // ... more members
  ];
  
  // Function to create a member element
  function createMemberElement(memberName, index) {
    var memberDiv = document.createElement('div');
    memberDiv.className = 'member';
  
    var checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.id = 'member' + index;
  
    var label = document.createElement('label');
    label.htmlFor = 'member' + index;
    label.textContent = memberName; // Use actual member name here
  
    var input = document.createElement('input');
    input.type = 'text';
    input.className = 'member-name';
    input.value = memberName; // Pre-fill with member name
  
    memberDiv.appendChild(checkbox);
    memberDiv.appendChild(input);
    memberDiv.appendChild(label);
  
    return memberDiv;
  }
  
  // Function to populate the list
  function populateMemberList(members) {
    var memberListDiv = document.getElementById('memberList');
    members.forEach(function(memberName, index) {
      var memberDiv = createMemberElement(memberName, index);
      memberListDiv.appendChild(memberDiv);
    });
  }
  
  // Call populateMemberList with the fetched members
  populateMemberList(moduleTeamMembers);
  