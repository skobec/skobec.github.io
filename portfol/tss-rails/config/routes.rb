Rails.application.routes.draw do
  get 'page/contact'
  post 'page/contact' => 'page#send_feedback', as: :send_feedback
  get 'page/help_transport'
  get 'page/object'
  get 'page/online_applic'
  get 'page/photo_object_cotel'
  get 'page/photo_object_optica'
  get 'page/photo_object_sochi'
  get 'page/photo_object_transformer'
  get 'page/reference'
  get 'page/sro'
  get 'page/technolog_park'

  root 'page#index'
end
