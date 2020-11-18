document.querySelectorAll('[data-condition]').forEach(field => {
	let condition = JSON.parse(field.getAttribute('data-condition'));
	collect(field, condition.item, condition.value);
});

function collect(elem, c_name, c_value) {
	document.querySelectorAll('input[name="' + c_name + '"]').forEach(c_elem => {
		if (c_elem) {
			switch (c_elem.type) {
				case  'radio':
				case 'checkbox':
					if (c_elem.checked && c_elem.value === '' + c_value) {
						elem.classList.remove('em-hidden-field');
						elem.classList.add('em-margin-left');
					}
					c_elem.addEventListener('click', function () {
						checkboxListener(elem, c_elem, c_value);
					});
					break;
			}
		}
	});
}

function checkboxListener(elem, c_elem, c_value) {
	if (c_elem.checked && c_elem.value === '' + c_value) {
		elem.classList.remove('em-hidden-field');
		elem.classList.add('em-margin-left');
	} else {
		elem.classList.add('em-hidden-field');
		elem.classList.remove('em-margin-left');
		elem.querySelectorAll('input').forEach(itm => itm.value = '');
	}
}

document.querySelectorAll('.checkNum').forEach(function (el) {
	el.addEventListener("input", elem => el.value = (isNaN(el.value)) ? el.value.replace(elem.data, '') : el.value);
})

// hide OJS contributor role and select the first role per default
if (document.querySelectorAll('.hideFormElements'))
	document.querySelectorAll('.hideFormElements')
		.forEach(hidden => JSON.parse(hidden.value)
			.forEach(elem => document.querySelectorAll('[id^="' + elem + '"]')
				.forEach(e => {
					if (elem === "userGroupId")
						e.querySelector("input").checked = true;  // check first radio btn;
					e.style.display = 'none';
				})));



