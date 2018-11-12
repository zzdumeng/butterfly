import $ from 'jquery';

const nameRe = /^[a-zA-Z]{1}\w{7,15}$/

const nameEl = $('#name')
const pwEl = $('#pw')
const submitEl = $('#login')

let nameValid = false
let pwValid = false

// TODO: the method to check if can submit
// may be neither efficient nor efficient.
// consider a better ux experience. 

nameEl.on('input', (e) => {
  const l = $(this).val().trim().length 
  if(l>=8 && l <= 16) {
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

pwEl.on('input', (e) => {
  const l = $(this).val().trim().length 
  if(l>6) {
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

submitEl.on('click', (e)=> {
  // if(!nameRe.test(nameEl.val().trim()) ) {
  //   e.preventDefault()
  //   showHint('name')
  //   return
  // } 
  // if(!pwRe.test(pwEl.val().trim())) {
  //   e.preventDefault()
  //   showHint('password')
  //   return
  // }
  // else do nothing, the action is posted.
})