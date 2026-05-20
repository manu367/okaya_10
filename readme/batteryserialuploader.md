### Upload Flow

- User → Upload Excel  
- Excel → Client-side preview  
- Server → Validate  
- DB → Commit / Rollback  

### Backend flow
  get ALl Model Already then match with uploaded excels

### frontend flow
- When user upload the file 
- check it's once and show the all dat into modal box(preview box)
- upload file on the server 
- getiing the response ( Response format= json=>{ 
   data :[{data,error:true}]
   })
     - Error = msg and error = true all red 
     - Sucess = msg and error = false , nothings