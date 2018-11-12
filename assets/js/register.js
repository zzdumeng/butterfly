import $ from 'jquery';

// 8~16, start with char, can use digits, char, and _
const nameRe = /^[a-zA-Z]{1}\w{7,15}$/
const pwRe = /^ $/

const nameEl = $('#name')
const pwEl = $('#pw')
const submitEl = $('#login')

submitEl.on('click', (e)=> {
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