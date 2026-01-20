# Quick Start Guide - Intern Operations System

## 🚀 Get Started in 3 Steps

### Step 1: Extract the Files
Unzip the `intern-system.zip` file to your desired location.

### Step 2: Open in Browser
Simply double-click `index.html` or right-click and select "Open with Browser"

### Step 3: Start Using
The application is ready to use immediately!

---

## 📝 Quick Tutorial (5 minutes)

### Create Your First Intern
1. Click the **"Interns"** tab
2. Click **"Add Intern"** button
3. Fill in:
   - Name: `Alice Johnson`
   - Email: `alice@example.com`
   - Skills: `JavaScript, React, CSS`
4. Click **"Save Intern"**
5. Notice the intern is in **ONBOARDING** status

### Activate the Intern
1. In the interns table, click **"Activate"** button
2. Confirm the action
3. Intern status changes to **ACTIVE**

### Create a Task
1. Click the **"Tasks"** tab
2. Click **"Create Task"** button
3. Fill in:
   - Title: `Build Login Page`
   - Required Skills: `JavaScript, React`
   - Estimated Hours: `8`
4. Click **"Save Task"**

### Assign Task to Intern
1. Find your task in the tasks table
2. Click **"Assign"** button
3. Select `Alice Johnson` from dropdown
4. Click **"Assign Task"**
5. Task status changes to **IN_PROGRESS**

### Complete the Task
1. Click **"Complete"** button on the task
2. Confirm the action
3. Task status changes to **DONE**

### Check the Dashboard
1. Click **"Dashboard"** tab
2. See your statistics updated
3. View recent interns and tasks

### View Activity Logs
1. Click **"Logs"** tab
2. See all actions you've performed
3. Each log shows timestamp and details

---

## 🎯 Try These Features

### Test Email Uniqueness
1. Try creating another intern with `alice@example.com`
2. System will prevent duplicate emails

### Test Status Transitions
1. Exit Alice (click "Exit" button)
2. Try to activate her again
3. System will block this transition

### Test Skill Matching
1. Create intern with skills: `Python, Django`
2. Try to assign the React task
3. System will show "No eligible interns"

### Test Task Dependencies
1. Create Task A: "Design Database"
2. Create Task B: "Implement API" (Dependencies: TASK-001)
3. Try to complete Task B
4. System will block until Task A is done

---

## 💡 Tips

- **Filters**: Use status filter and skill search in Interns tab
- **Persistence**: All data saves automatically to your browser
- **Refresh**: Data persists even after refreshing the page
- **Clear Data**: Clear logs or use browser dev tools to reset
- **Dark Mode**: Not available yet (future enhancement)

---

## 🐛 Troubleshooting

**Problem**: Page is blank
- **Solution**: Ensure JavaScript is enabled in your browser

**Problem**: Changes not saving
- **Solution**: Check browser console for errors
- **Solution**: Ensure localStorage is enabled

**Problem**: Can't assign task
- **Solution**: Verify intern is ACTIVE
- **Solution**: Verify intern has matching skills

**Problem**: Can't complete task
- **Solution**: Check if task has unresolved dependencies

---

## 📚 Full Documentation

For complete documentation, see `README.md` in the project folder.

---

## ✅ What You've Accomplished

After this tutorial, you've:
- ✅ Created an intern
- ✅ Managed intern lifecycle
- ✅ Created a task
- ✅ Assigned a task
- ✅ Completed a task
- ✅ Viewed activity logs
- ✅ Understood the system flow

**You're now ready to explore all features!** 🎉

---

**Need Help?** Check the full README.md for detailed documentation.
