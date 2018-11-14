import $ from 'jquery';

// 8~16, start with char, can use digits, char, and _
const nameRe = /^[a-zA-Z]{1}\w{7,15}$/
const pwRe = /^\w{8,16}$/

const nameEl = $('#name')
const pwEl = $('#pw')
const submitEl = $('#register')

let nameValid
let pwValid

nameEl.on('input', function(e) {
  const l = $(this).val().trim() 
  if(nameRe.test(l)) {
    // length valid
    nameValid = true
  } else {
    nameValid = false
  }
  if(nameValid && pwValid) {
    submitEl.removeClass('disabled')
  } else {
    submitEl.addClass('disabled')
  }
})

pwEl.on('input', function(e) {
  const l = $(this).val().trim()
  if(pwRe.test(l)) {
    pwValid = true
  } else {
    pwValid = false
  }
  if(nameValid && pwValid) {
    submitEl.removeClass('disabled')
  } else {
    submitEl.addClass('disabled')
  }
})


submitEl.on('click', function(e) {
  if(!nameRe.test(nameEl.val().trim()) ) {
    e.preventDefault()
    showHint('name')
    return
  } 
  if(!pwRe.test(pwEl.val().trim())) {
    e.preventDefault()
    showHint('password')
    return
  }
  // else do nothing, the action is posted.
})

function showHint() {
  alert("not correct")
}
alert("register")